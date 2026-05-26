@extends('layouts.app')

@section('page-title', 'Admin Dashboard')

@section('content')
    <x-ui.page-header
        title="Admin Control Center"
        subtitle="Monitor users, farmer products, buyer orders, verification activity, and overall digital supply chain performance."
    />

    <div class="mb-5 flex justify-end">
        <x-ui.secondary-button href="{{ route('admin.system-report.print') }}">
            Print System Report
        </x-ui.secondary-button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <x-ui.stat-card label="Total Users" :value="$totalUsers ?? 0" icon="users" trend="All registered system accounts." />
        <x-ui.stat-card label="Pending Verifications" :value="$pendingUsersCount ?? 0" icon="clock" tone="amber" trend="Users waiting for admin review." />
        <x-ui.stat-card label="Farmers" :value="$totalFarmers ?? 0" icon="farmer" trend="Registered farmer sellers." />
        <x-ui.stat-card label="Buyers" :value="$totalBuyers ?? 0" icon="buyer" tone="blue" trend="Registered marketplace buyers." />
        <x-ui.stat-card label="Products" :value="$totalProducts ?? 0" icon="products" trend="Products listed in the marketplace." />
        <x-ui.stat-card label="Pending Products" :value="$pendingProducts ?? 0" icon="alert" tone="amber" trend="Products needing stock or listing attention." />
        <x-ui.stat-card label="Total Orders" :value="$totalOrders ?? 0" icon="orders" tone="blue" trend="Farmer-to-buyer transactions." />
        <x-ui.stat-card label="Completed Orders" :value="$completedOrders ?? 0" icon="check" tone="green" trend="Successfully fulfilled orders." />
        <x-ui.stat-card label="Cancelled Orders" :value="$cancelledOrders ?? 0" icon="x" tone="red" trend="Transactions that did not proceed." />
        <x-ui.stat-card label="Total Revenue" value="PHP {{ number_format($totalRevenue ?? 0, 2) }}" icon="money" tone="green" trend="Completed marketplace sales." />
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
        <x-ui.table-card title="Pending Verification" subtitle="Users waiting for approval in the supply chain platform.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Role</th>
                    <th class="px-5 py-3">Registered</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pendingUsers ?? [] as $pendingUser)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900">{{ $pendingUser->name }}</p>
                            <p class="text-sm text-slate-500">{{ $pendingUser->email }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ \Illuminate\Support\Str::title($pendingUser->role) }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ optional($pendingUser->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                        <td class="px-5 py-4"><x-ui.status-badge :status="$pendingUser->verification_status ?? 'Pending'" /></td>
                        <td class="px-5 py-4">
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.users.approve', $pendingUser) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100 dark:bg-emerald-900/40 dark:text-emerald-200 dark:hover:bg-emerald-900/60">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.reject', $pendingUser) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 transition hover:bg-red-100 dark:bg-red-900/40 dark:text-red-200 dark:hover:bg-red-900/60" onclick="return confirm('Reject this registration?')">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-5">
                            <x-ui.empty-state title="No pending verifications." message="All farmer and buyer accounts are currently reviewed." icon="check" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.dashboard-card title="Activity Logs Preview" subtitle="Recent system activity across users, products, and orders.">
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
        <x-ui.table-card title="Recent Orders" subtitle="Latest marketplace transactions monitored by the admin.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3">Buyer</th>
                    <th class="px-5 py-3">Farmer</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Amount</th>
                    <th class="px-5 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentOrders ?? [] as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">#{{ $order->id }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $order->consumer->name ?? 'Buyer' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ optional($order->items->first()?->farmer)->name ?? 'Multiple farmers' }}</td>
                        <td class="px-5 py-4"><x-ui.status-badge :status="$order->status" /></td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">PHP {{ number_format($order->total, 2) }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ optional($order->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-5"><x-ui.empty-state title="No orders yet" message="Orders will appear after buyers complete checkout." icon="orders" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.dashboard-card title="Quick Actions" subtitle="Admin shortcuts for monitoring and management.">
            <div class="grid gap-3">
                <x-ui.quick-action-card href="{{ route('admin.user-reports') }}" title="User Reports" description="Review buyer and farmer accounts." icon="users" />
                <x-ui.quick-action-card href="{{ route('admin.user-reports') }}" title="Verify Farmers" description="Approve or reject verification requests." icon="check" />
                <x-ui.quick-action-card href="{{ route('admin.products') }}" title="Manage Products" description="Review marketplace listings." icon="products" />
                <x-ui.quick-action-card href="{{ route('orders.index') }}" title="View Orders" description="Monitor order fulfillment." icon="orders" />
                <x-ui.quick-action-card href="{{ route('admin.user-reports') }}" title="Generate Reports" description="Review user verification and account summaries." icon="report" />
                <x-ui.quick-action-card href="{{ route('admin.announcements.index') }}" title="Manage Announcements" description="Publish platform updates." icon="megaphone" />
                <x-ui.quick-action-card href="{{ route('admin.activity-logs') }}" title="View Activity Logs" description="Audit marketplace activity." icon="activity" />
            </div>
        </x-ui.dashboard-card>
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <x-ui.dashboard-card title="Pending Products" subtitle="Products needing listing or stock attention.">
            @forelse($pendingProductsList ?? [] as $product)
                <x-ui.alert-card class="mb-3" title="{{ $product->name }}" message="Current stock: {{ $product->quantity }} {{ $product->unit ?? 'piece' }}. Farmer: {{ $product->farmer->name ?? 'Unknown' }}." tone="red" />
            @empty
                <x-ui.empty-state title="No pending products" message="No out-of-stock product listings are pending attention." icon="products" />
            @endforelse
        </x-ui.dashboard-card>

        <x-ui.dashboard-card title="Low-Stock Products" subtitle="Inventory monitoring for product availability.">
            @forelse($lowStockProducts ?? [] as $product)
                <x-ui.alert-card class="mb-3" title="Low Stock" message="{{ $product->name }} has {{ $product->quantity }} {{ $product->unit ?? 'piece' }} remaining. Farmer: {{ $product->farmer->name ?? 'Unknown' }}." tone="{{ $product->quantity <= 0 ? 'red' : 'amber' }}" />
            @empty
                <x-ui.empty-state title="Inventory healthy" message="No low-stock products were found." icon="check" />
            @endforelse
        </x-ui.dashboard-card>

        <x-ui.dashboard-card title="Recently Added Products" subtitle="Latest farmer product listings.">
            <div class="space-y-3">
                @forelse($recentProducts ?? [] as $product)
                    <div class="rounded-xl border border-slate-100 p-3">
                        <p class="font-bold text-slate-900">{{ $product->name }}</p>
                        <p class="text-sm text-slate-500">{{ $product->farmer->name ?? 'Local Farmer' }} - PHP {{ number_format($product->price, 2) }} / {{ $product->unit ?? 'piece' }}</p>
                        <div class="mt-2"><x-ui.status-badge :status="$product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock')" /></div>
                    </div>
                @empty
                    <x-ui.empty-state title="No products yet" message="Recently added products will appear here." icon="products" />
                @endforelse
            </div>
        </x-ui.dashboard-card>
    </div>
@endsection
