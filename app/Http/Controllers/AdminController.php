<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Feedback;
use App\Models\Product;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

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

    public function printSystemReport()
    {
        return view('prints.admin-system-report', [
            'reportDate' => now(),
            'totalUsers' => User::count(),
            'totalFarmers' => User::where('role', 'farmer')->count(),
            'totalBuyers' => User::whereIn('role', ['consumer', 'buyer'])->count(),
            'totalProducts' => Product::count(),
            'pendingVerifications' => User::whereIn('role', ['farmer', 'consumer', 'buyer'])
                ->get()
                ->filter(fn (User $user) => $this->isPendingVerificationUser($user))
                ->count(),
            'totalOrders' => Order::count(),
            'completedOrders' => Order::where('status', 'completed')->count(),
            'cancelledOrders' => Order::where('status', 'cancelled')->count(),
            'totalRevenue' => Order::where('status', 'completed')->sum('total'),
        ]);
    }

    public function printProductsReport()
    {
        return view('prints.admin-products-report', [
            'reportDate' => now(),
            'products' => Product::with('farmer')->latest()->get(),
        ]);
    }

    public function printOrdersReport()
    {
        return view('prints.admin-orders-report', [
            'reportDate' => now(),
            'orders' => Order::with(['consumer', 'items.farmer'])->withCount('items')->latest()->get(),
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

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending_verification,paid,rejected',
        ]);

        if ($order->payment_method !== 'gcash') {
            return back()->with('error', 'Only GCash payments require verification.');
        }

        if ($validated['payment_status'] === 'paid' && ! $order->payment_proof) {
            return back()->with('error', 'No proof of payment uploaded.');
        }

        $order->update([
            'payment_status' => $validated['payment_status'],
        ]);

        return back()->with('success', 'Payment status updated successfully.');
    }

    public function markPaymentPaid(Order $order, NotificationService $notifications)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($order->payment_method !== 'gcash') {
            return back()->with('error', 'Only GCash payments require verification.');
        }

        if (! $order->payment_proof) {
            return back()->with('error', 'No proof of payment uploaded.');
        }

        $order->update([
            'payment_status' => 'paid',
        ]);

        $order->loadMissing('consumer');

        if ($order->consumer) {
            $notifications->send(
                $order->consumer,
                'payment.verified',
                'GCash payment verified',
                "Your GCash payment for Order #{$order->id} has been verified.",
                'check',
                route('orders.show', $order),
                ['order_id' => $order->id, 'payment_status' => 'paid']
            );
        }

        return back()->with('success', 'GCash payment marked as paid.');
    }

    public function rejectPayment(Order $order, NotificationService $notifications)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($order->payment_method !== 'gcash') {
            return back()->with('error', 'Only GCash payments can be rejected.');
        }

        $order->update([
            'payment_status' => 'rejected',
        ]);

        $order->loadMissing('consumer');

        if ($order->consumer) {
            $notifications->send(
                $order->consumer,
                'payment.rejected',
                'GCash payment rejected',
                "Your GCash proof for Order #{$order->id} was rejected. Please contact admin for assistance.",
                'alert',
                route('orders.show', $order),
                ['order_id' => $order->id, 'payment_status' => 'rejected']
            );
        }

        return back()->with('success', 'GCash payment rejected.');
    }

    public function approveUser(User $user, NotificationService $notifications)
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

        $notifications->send(
            $user,
            'user.approved',
            'Account approved',
            'Your SariwaLink account has been approved. You can now use your dashboard.',
            'check',
            route('dashboard')
        );

        return back()->with('success', 'User approved successfully.');
    }

    public function rejectUser(User $user, NotificationService $notifications)
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

        $notifications->send(
            $user,
            'user.rejected',
            'Account verification rejected',
            'Your SariwaLink account verification was rejected. Please contact the administrator for assistance.',
            'alert',
            route('dashboard')
        );

        return back()->with('success', 'User rejected successfully.');
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
            && $user->verification_status === 'pending';
    }
}
