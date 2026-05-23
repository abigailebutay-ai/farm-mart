<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Feedback;
use App\Models\Product;
use App\Models\User;

class AdminController extends Controller
{
    public function products()
    {
        $products = Product::with('farmer')->latest()->paginate(15);

        return view('admin.products', [
            'products' => $products,
        ]);
    }

    public function showProduct(Product $product)
    {
        $product->load('farmer');

        return view('admin.products.show', [
            'product' => $product,
        ]);
    }

    public function userReports()
    {
        $users = User::whereIn('role', ['farmer', 'consumer', 'buyer'])
            ->latest()
            ->get();

        return view('admin.user-reports', [
            'users' => $users,
            'totalFarmers' => $users->where('role', 'farmer')->count(),
            'totalBuyers' => $users->whereIn('role', ['consumer', 'buyer'])->count(),
            'pendingVerifications' => $users
                ->filter(fn (User $user) => $this->isPendingVerificationUser($user))
                ->count(),
            'recentFeedback' => Feedback::with(['user', 'order'])->latest()->limit(20)->get(),
            'unreadFeedbackCount' => Feedback::where('status', 'unread')->count(),
        ]);
    }

    public function markFeedbackRead(Feedback $feedback)
    {
        $feedback->update(['status' => 'read']);

        return back()->with('success', 'Feedback marked as read.');
    }

    public function resolveFeedback(Feedback $feedback)
    {
        $feedback->update(['status' => 'resolved']);

        return back()->with('success', 'Feedback marked as resolved.');
    }

    public function approveUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts do not need approval.');
        }

        if (! in_array($user->role, ['farmer', 'consumer', 'buyer'], true)) {
            return back()->with('error', 'Only farmer and buyer accounts can be approved.');
        }

        $user->update([
            'is_verified' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        return back()->with('success', 'User approved successfully.');
    }

    public function rejectUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be rejected.');
        }

        if (! in_array($user->role, ['farmer', 'consumer', 'buyer'], true)) {
            return back()->with('error', 'Only farmer and buyer accounts can be rejected.');
        }

        $user->update([
            'is_verified' => false,
            'verification_status' => 'rejected',
            'verified_at' => null,
        ]);

        return back()->with('success', 'User rejected successfully.');
    }

    public function announcements()
    {
        return view('admin.announcements', [
            'announcements' => collect(),
        ]);
    }

    public function activityLogs()
    {
        $activityLogs = collect()
            ->merge(
                User::whereIn('role', ['farmer', 'consumer', 'buyer'])->latest()->limit(8)->get()->map(fn (User $user) => [
                    'title' => 'User registered',
                    'description' => "{$user->name} joined as " . ucfirst($user->role) . '.',
                    'date' => $user->created_at,
                    'icon' => 'users',
                ])
            )
            ->merge(
                Product::with('farmer')->latest()->limit(8)->get()->map(fn (Product $product) => [
                    'title' => 'Product listed',
                    'description' => ($product->farmer->name ?? 'A farmer') . " listed {$product->name}.",
                    'date' => $product->created_at,
                    'icon' => 'products',
                ])
            )
            ->merge(
                Order::with('consumer')->latest()->limit(8)->get()->map(fn (Order $order) => [
                    'title' => 'Order activity',
                    'description' => ($order->consumer->name ?? 'A buyer') . " placed Order #{$order->id}.",
                    'date' => $order->created_at,
                    'icon' => 'orders',
                ])
            )
            ->merge(
                Feedback::with('user')->latest()->limit(8)->get()->map(fn (Feedback $feedback) => [
                    'title' => 'Buyer feedback submitted',
                    'description' => ($feedback->user->name ?? 'A buyer') . " rated {$feedback->feedback_type} {$feedback->rating}/5.",
                    'date' => $feedback->created_at,
                    'icon' => 'star',
                ])
            )
            ->sortByDesc('date')
            ->values();

        return view('admin.activity-logs', [
            'activityLogs' => $activityLogs,
        ]);
    }

    private function isPendingVerificationUser(User $user): bool
    {
        return in_array($user->role, ['farmer', 'consumer', 'buyer'], true)
            && ($user->verification_status === 'pending' || (! $user->is_verified && $user->verification_status === null));
    }
}
