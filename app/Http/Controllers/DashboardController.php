<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        if ($user->isFarmer()) {
            return $this->farmerDashboard($user);
        }

        if ($user->isConsumer()) {
            return $this->consumerDashboard($user);
        }

        abort(403, 'Unauthorized role.');
    }

    /**
     * Admin dashboard.
     */
    private function adminDashboard()
    {
        $pendingUsersQuery = $this->pendingVerificationUsersQuery();

        $pendingUsers = (clone $pendingUsersQuery)
            ->latest()
            ->limit(5)
            ->get();

        $recentOrders = Order::with(['consumer', 'items.farmer'])
            ->latest()
            ->limit(5)
            ->get();

        $recentProducts = Product::with('farmer')
            ->latest()
            ->limit(5)
            ->get();

        $activityLogs = collect()
            ->merge(
                User::whereIn('role', ['farmer', 'consumer', 'buyer'])->latest()->limit(3)->get()->map(fn ($user) => [
                    'title' => 'User registered',
                    'description' => "{$user->name} joined as " . ucfirst($user->role) . '.',
                    'date' => $user->created_at,
                    'icon' => 'users',
                ])
            )
            ->merge(
                Product::with('farmer')->latest()->limit(3)->get()->map(fn ($product) => [
                    'title' => 'Farmer added product',
                    'description' => ($product->farmer->name ?? 'A farmer') . " listed {$product->name}.",
                    'date' => $product->created_at,
                    'icon' => 'products',
                ])
            )
            ->merge(
                Order::with('consumer')->latest()->limit(3)->get()->map(fn ($order) => [
                    'title' => 'Buyer placed order',
                    'description' => ($order->consumer->name ?? 'A buyer') . " placed Order #{$order->id}.",
                    'date' => $order->created_at,
                    'icon' => 'orders',
                ])
            )
            ->sortByDesc('date')
            ->take(6)
            ->values();

        return view('dashboard.admin', [
            'totalUsers' => User::count(),
            'pendingUsersCount' => (clone $pendingUsersQuery)->count(),
            'totalFarmers' => User::where('role', 'farmer')->count(),
            'totalBuyers' => User::whereIn('role', ['consumer', 'buyer'])->count(),
            'totalProducts' => Product::count(),
            'pendingProducts' => Product::where('quantity', '<=', 0)->count(),
            'totalOrders' => Order::count(),
            'completedOrders' => Order::where('status', 'completed')->count(),
            'cancelledOrders' => Order::where('status', 'cancelled')->count(),
            'totalRevenue' => Order::where('status', 'completed')->sum('total'),
            'recentOrders' => $recentOrders,
            'pendingUsers' => $pendingUsers,
            'pendingProductsList' => Product::with('farmer')->where('quantity', '<=', 0)->latest()->limit(5)->get(),
            'lowStockProducts' => Product::with('farmer')->where('quantity', '<=', 10)->orderBy('quantity')->limit(5)->get(),
            'recentProducts' => $recentProducts,
            'activityLogs' => $activityLogs,
        ]);
    }

    /**
     * Users that require admin verification.
     */
    private function pendingVerificationUsersQuery()
    {
        return User::query()
            ->whereIn('role', ['farmer', 'consumer', 'buyer'])
            ->where(function ($query) {
                $query->where('verification_status', 'pending')
                    ->orWhere(function ($query) {
                        $query->where('is_verified', false)
                            ->whereNull('verification_status');
                    });
            });
    }

    /**
     * Farmer dashboard.
     */
    private function farmerDashboard($user)
    {
        $farmerOrderQuery = Order::whereHas('items', function ($query) use ($user) {
            $query->where('farmer_id', $user->id);
        });

        $totalSales = OrderItem::where('farmer_id', $user->id)
            ->whereHas('order', fn ($query) => $query->where('status', 'completed'))
            ->sum('subtotal');

        $monthlySales = OrderItem::where('farmer_id', $user->id)
            ->whereHas('order', fn ($query) => $query->where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year))
            ->sum('subtotal');

        return view('dashboard.farmer', [
            'totalProducts' => $user->products()->count(),
            'totalInventoryQuantity' => $user->products()->sum('quantity'),
            'lowStockProductsCount' => $user->products()->whereBetween('quantity', [1, 10])->count(),
            'pendingOrders' => (clone $farmerOrderQuery)->where('status', 'pending')->count(),
            'completedOrders' => (clone $farmerOrderQuery)->where('status', 'completed')->count(),
            'totalSales' => $totalSales,
            'monthlySales' => $monthlySales,
            'recentOrders' => $farmerOrderQuery->with('consumer', 'items')->latest()->limit(5)->get(),
            'recentProducts' => $user->products()->latest()->limit(5)->get(),
        ]);
    }

    /**
     * Farmer decision support page.
     */
    public function farmerDecisionSupport()
    {
        return view('dashboard.farmer-decision-support', $this->farmerInsightData(auth()->user()));
    }

    /**
     * Farmer sales summary page.
     */
    public function farmerSalesSummary()
    {
        $user = auth()->user();
        $completedItems = $this->farmerCompletedSalesItems($user);

        $monthlyRows = $completedItems
            ->groupBy(fn ($item) => optional($item->created_at)->timezone(config('app.timezone'))->format('F Y') ?? 'Unspecified')
            ->map(fn ($items, $month) => [
                'month' => $month,
                'orders' => $items->pluck('order_id')->unique()->count(),
                'quantity' => $items->sum('quantity'),
                'sales' => $items->sum('subtotal'),
            ])
            ->values();

        return view('dashboard.farmer-sales-summary', [
            'totalSales' => $completedItems->sum('subtotal'),
            'totalSoldQuantity' => $completedItems->sum('quantity'),
            'completedOrderCount' => $completedItems->pluck('order_id')->unique()->count(),
            'monthlyRows' => $monthlyRows,
            'recentSales' => $completedItems->take(8),
        ]);
    }

    /**
     * Printable farmer sales summary report.
     */
    public function printFarmerSalesSummary()
    {
        $user = auth()->user();
        $completedItems = $this->farmerCompletedSalesItems($user);

        $productRows = $completedItems
            ->groupBy(fn ($item) => $item->product_id ?: 'deleted-' . ($item->product->name ?? 'product'))
            ->map(function ($items) {
                $firstItem = $items->first();
                $product = $firstItem->product;

                return [
                    'name' => $product->name ?? 'Product unavailable',
                    'unit' => $product->unit ?? 'piece',
                    'quantity' => $items->sum('quantity'),
                    'revenue' => $items->sum('subtotal'),
                ];
            })
            ->sortBy('name')
            ->values();

        return view('prints.farmer-sales-summary', [
            'farmer' => $user,
            'dateGenerated' => now(),
            'totalSales' => $completedItems->sum('subtotal'),
            'completedOrderCount' => $completedItems->pluck('order_id')->unique()->count(),
            'soldProductsCount' => $productRows->count(),
            'productRows' => $productRows,
        ]);
    }

    private function farmerCompletedSalesItems($user)
    {
        return OrderItem::with(['product', 'order'])
            ->where('farmer_id', $user->id)
            ->whereHas('order', fn ($query) => $query->where('status', 'completed'))
            ->latest()
            ->get();
    }

    /**
     * Shared farmer analytics for decision support.
     */
    private function farmerInsightData($user): array
    {
        $bestSellingProducts = Product::query()
            ->select('products.*', DB::raw('COALESCE(SUM(order_items.quantity), 0) as sold_quantity'), DB::raw('COALESCE(SUM(order_items.subtotal), 0) as sales_amount'))
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->where('products.user_id', $user->id)
            ->groupBy('products.id', 'products.user_id', 'products.name', 'products.description', 'products.category', 'products.price', 'products.quantity', 'products.unit', 'products.image', 'products.status', 'products.low_stock_threshold', 'products.harvest_date', 'products.crop_code', 'products.average_rating', 'products.total_reviews', 'products.created_at', 'products.updated_at')
            ->orderByDesc('sold_quantity')
            ->get();

        $monthlySales = OrderItem::where('farmer_id', $user->id)
            ->whereHas('order', fn ($query) => $query->where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year))
            ->sum('subtotal');

        $lowStockProducts = $user->products()->whereBetween('quantity', [1, 10])->orderBy('quantity')->get();
        $outOfStockProducts = $user->products()->where('quantity', '<=', 0)->orderBy('name')->get();
        $productsWithNoOrders = $user->products()->doesntHave('orderItems')->limit(5)->get();
        $slowMovingProducts = $bestSellingProducts->where('sold_quantity', '<=', 2)->take(5);

        return [
            'bestSellingProducts' => $bestSellingProducts->where('sold_quantity', '>', 0)->take(5),
            'bestSellingProduct' => $bestSellingProducts->firstWhere('sold_quantity', '>', 0),
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'productsWithNoOrders' => $productsWithNoOrders,
            'slowMovingProducts' => $slowMovingProducts,
            'monthlySales' => $monthlySales,
            'suggestedRestockQuantity' => $lowStockProducts->sum(fn ($product) => max(20 - (int) $product->quantity, 0)),
            'productPerformance' => $bestSellingProducts->take(10),
        ];
    }

    /**
     * Consumer dashboard.
     */
    private function consumerDashboard($user)
    {
        $cart = $user->cart;
        $cartItemsCount = $cart?->items()->count() ?? 0;
        $recentOrders = $user->orders()->with('items')->latest()->limit(5)->get();
        $recentCompletedOrders = $user->orders()
            ->with('items')
            ->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get();
        $recommendedProducts = Product::with('farmer')->where('quantity', '>', 0)->latest()->limit(6)->get();

        return view('dashboard.consumer', [
            'availableProducts' => Product::where('quantity', '>', 0)->count(),
            'cartItemsCount' => $cartItemsCount,
            'pendingOrders' => $user->orders()->where('status', 'pending')->count(),
            'completedOrders' => $user->orders()->where('status', 'completed')->count(),
            'totalPurchases' => $user->orders()->count(),
            'totalSpent' => $user->orders()->where('status', 'completed')->sum('total'),
            'completedOrdersForFeedback' => $user->orders()->where('status', 'completed')->count(),
            'recentOrders' => $recentOrders,
            'recentCompletedOrders' => $recentCompletedOrders,
            'recommendedProducts' => $recommendedProducts,
        ]);
    }
}
