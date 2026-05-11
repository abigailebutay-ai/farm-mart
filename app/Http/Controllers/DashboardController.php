<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isFarmer()) {
            return $this->farmerDashboard($user);
        } else {
            return $this->consumerDashboard($user);
        }
    }

    /**
     * Farmer dashboard.
     */
    private function farmerDashboard($user)
    {
        $totalProducts = $user->products()->count();
        $totalSales = Order::whereHas('items', function ($query) use ($user) {
            $query->where('farmer_id', $user->id);
        })->where('status', 'completed')->count();
        $pendingOrders = Order::whereHas('items', function ($query) use ($user) {
            $query->where('farmer_id', $user->id);
        })->where('status', 'pending')->count();
        $recentOrders = Order::whereHas('items', function ($query) use ($user) {
            $query->where('farmer_id', $user->id);
        })->latest()->limit(5)->get();

        return view('dashboard.farmer', [
            'totalProducts' => $totalProducts,
            'totalSales' => $totalSales,
            'pendingOrders' => $pendingOrders,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * Consumer dashboard.
     */
    private function consumerDashboard($user)
    {
        $cart = $user->cart;
        $cartItemsCount = $cart?->items()->count() ?? 0;
        $recentOrders = $user->orders()->latest()->limit(5)->get();
        $recommendedProducts = Product::inRandomOrder()->limit(6)->get();

        return view('dashboard.consumer', [
            'cartItemsCount' => $cartItemsCount,
            'recentOrders' => $recentOrders,
            'recommendedProducts' => $recommendedProducts,
        ]);
    }
}
