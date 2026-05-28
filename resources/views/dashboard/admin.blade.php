@extends('layouts.app')

@section('page-title', 'Admin Dashboard')

@section('content')
    <x-ui.page-header
        title="Admin Dashboard"
        subtitle="Review users, products, orders, payments, and system activity from one place."
    />

    <div class="mb-5 flex justify-end">
        <x-ui.secondary-button href="{{ route('admin.system-report.print') }}">
            Print System Report
        </x-ui.secondary-button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <x-ui.stat-card label="Total Users" :value="$totalUsers ?? 0" icon="users" trend="All registered accounts." />
        <x-ui.stat-card label="Pending User Approvals" :value="$pendingUsersCount ?? 0" icon="clock" tone="amber" trend="Users waiting for admin approval." />
        <x-ui.stat-card label="Farmers" :value="$totalFarmers ?? 0" icon="farmer" trend="Registered farmer sellers." />
        <x-ui.stat-card label="Consumers" :value="$totalBuyers ?? 0" icon="buyer" tone="blue" trend="Registered marketplace buyers." />
        <x-ui.stat-card label="Products" :value="$totalProducts ?? 0" icon="products" trend="Products listed by farmers." />
        <x-ui.stat-card label="Out of Stock Products" :value="$pendingProducts ?? 0" icon="alert" tone="amber" trend="Products that need farmer attention." />
        <x-ui.stat-card label="Total Orders" :value="$totalOrders ?? 0" icon="orders" tone="blue" trend="All buyer orders." />
        <x-ui.stat-card label="Completed Orders" :value="$completedOrders ?? 0" icon="check" tone="green" trend="Orders marked completed." />
        <x-ui.stat-card label="Cancelled Orders" :value="$cancelledOrders ?? 0" icon="x" tone="red" trend="Orders that did not continue." />
        <x-ui.stat-card label="Total Revenue" value="PHP {{ number_format($totalRevenue ?? 0, 2) }}" icon="money" tone="green" trend="Sales from completed orders." />
        <x-ui.stat-card label="GCash Payments to Review" :value="$gcashPaymentsToReview ?? 0" icon="money" tone="amber" trend="Uploaded payment proofs waiting for verification." />
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
        <x-ui.table-card title="Pending User Approvals" subtitle="Review new farmer and consumer accounts before they can fully use the system.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Role</th>
                    <th class="px-5 py-3">Date Registered</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pendingUsers ?? [] as $pendingUser)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900">{{ $pendingUser->name }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $pendingUser->email }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ \Illuminate\Support\Str::title($pendingUser->role) }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ optional($pendingUser->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                        <td class="px-5 py-4"><x-ui.status-badge :status="$pendingUser->verification_status ?? 'Pending'" /></td>
                        <td class="px-5 py-4">
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.users.approve', $pendingUser) }}" onsubmit="return confirm('Approve this user account?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100 dark:bg-emerald-900/40 dark:text-emerald-200 dark:hover:bg-emerald-900/60">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.reject', $pendingUser) }}" onsubmit="return confirm('Reject this user account?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 transition hover:bg-red-100 dark:bg-red-900/40 dark:text-red-200 dark:hover:bg-red-900/60">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-5">
                            <x-ui.empty-state title="No users waiting for approval." message="New farmer and consumer registrations will appear here." icon="check" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.dashboard-card title="Recent Activity" subtitle="Recent registrations, product updates, and order activity.">
            <div class="space-y-3">
                @forelse($activityLogs ?? [] as $activity)
                    <div class="flex gap-3 rounded-xl border border-slate-100 p-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800">
                            <x-ui.icon :name="$activity['icon'] ?? 'activity'" />
                        </span>
                        <div>
                            <p class="font-bold text-slate-900">{{ $activity['title'] ?? 'System activity' }}</p>
                            <p class="text-sm text-slate-500">{{ $activity['description'] ?? 'Activity details unavailable.' }}</p>
                            <p class="mt-1 text-xs font-medium text-amber-600">{{ optional($activity['date'] ?? null)->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state title="No activity yet" message="Recent registrations, products, and orders will appear here." icon="activity" />
                @endforelse
            </div>
        </x-ui.dashboard-card>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-[1.2fr_0.8fr]">
        <x-ui.table-card title="Recent Orders" subtitle="Latest buyer orders and payment status.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Order No.</th>
                    <th class="px-5 py-3">Buyer</th>
                    <th class="px-5 py-3">Payment</th>
                    <th class="px-5 py-3">Order Status</th>
                    <th class="px-5 py-3">Total Amount</th>
                    <th class="px-5 py-3">Order Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentOrders ?? [] as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">#{{ $order->id }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $order->consumer->name ?? 'Buyer' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $order->paymentMethodLabel() }} - {{ $order->paymentStatusLabel() }}</td>
                        <td class="px-5 py-4"><x-ui.status-badge :status="$order->status" /></td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">PHP {{ number_format($order->total, 2) }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ optional($order->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-5"><x-ui.empty-state title="No orders yet" message="Orders will appear after buyers complete checkout." icon="orders" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.dashboard-card title="Quick Actions" subtitle="Common admin tasks.">
            <div class="grid gap-3">
                <x-ui.quick-action-card href="{{ route('admin.user-reports') }}" title="User Approvals" description="Approve or reject new user accounts." icon="users" />
                <x-ui.quick-action-card href="{{ route('admin.products') }}" title="Products" description="View products listed by farmers." icon="products" />
                <x-ui.quick-action-card href="{{ route('orders.index') }}" title="Orders" description="Review order status and payment information." icon="orders" />
                <x-ui.quick-action-card href="{{ route('orders.index') }}" title="Payment Verification" description="Check GCash proofs waiting for review." icon="money" />
                <x-ui.quick-action-card href="{{ route('admin.announcements.index') }}" title="Announcements" description="Create announcements for users." icon="megaphone" />
                <x-ui.quick-action-card href="{{ route('admin.activity-logs') }}" title="Activity Logs" description="View recent system actions." icon="activity" />
            </div>
        </x-ui.dashboard-card>
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <x-ui.dashboard-card title="Out of Stock Products" subtitle="Products that currently have no available stock.">
            @forelse($pendingProductsList ?? [] as $product)
                <x-ui.alert-card class="mb-3" title="{{ $product->name }}" message="Current stock: {{ $product->quantity }} kg. Farmer: {{ $product->farmer->name ?? 'Unknown' }}." tone="red" />
            @empty
                <x-ui.empty-state title="No pending products" message="No out-of-stock product listings are pending attention." icon="products" />
            @endforelse
        </x-ui.dashboard-card>

        <x-ui.dashboard-card title="Low Stock Products" subtitle="Products that may need restocking soon.">
            @forelse($lowStockProducts ?? [] as $product)
                <x-ui.alert-card class="mb-3" title="Low Stock" message="{{ $product->name }} has {{ $product->quantity }} kg remaining. Farmer: {{ $product->farmer->name ?? 'Unknown' }}." tone="{{ $product->quantity <= 0 ? 'red' : 'amber' }}" />
            @empty
                <x-ui.empty-state title="Inventory healthy" message="No low-stock products were found." icon="check" />
            @endforelse
        </x-ui.dashboard-card>

        <x-ui.dashboard-card title="Recently Added Products" subtitle="Latest farmer product listings.">
            <div class="space-y-3">
                @forelse($recentProducts ?? [] as $product)
                    <div class="rounded-xl border border-slate-100 p-3">
                        <p class="font-bold text-slate-900">{{ $product->name }}</p>
                        <p class="text-sm text-slate-500">{{ $product->farmer->name ?? 'Local Farmer' }} - PHP {{ number_format($product->price, 2) }} / kg</p>
                        <div class="mt-2"><x-ui.status-badge :status="$product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock')" /></div>
                    </div>
                @empty
                    <x-ui.empty-state title="No products yet" message="Recently added products will appear here." icon="products" />
                @endforelse
            </div>
        </x-ui.dashboard-card>
    </div>
@endsection
