<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Order;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Show the home page.
     */
    public function index()
    {
        $featuredProducts = Product::with('farmer')
            ->where('quantity', '>', 0)
            ->latest()
            ->limit(8)
            ->get();

        $monthlyCompletedOrders = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);

        return view('home', [
            'featuredProducts' => $featuredProducts,
            'marketplaceProductCount' => Product::count(),
            'lowStockCount' => Product::whereBetween('quantity', [1, 10])->count(),
            'restockNeededCount' => Product::where('quantity', '<=', 10)->count(),
            'availableProductCount' => Product::where('quantity', '>', 0)->count(),
            'monthlySalesTotal' => (clone $monthlyCompletedOrders)->sum('total'),
            'monthlyCompletedOrdersCount' => (clone $monthlyCompletedOrders)->count(),
            'recentOrder' => Order::with(['consumer', 'items.product'])
                ->latest()
                ->first(),
            'publishedAnnouncements' => Announcement::where('status', 'published')
                ->latest('published_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
