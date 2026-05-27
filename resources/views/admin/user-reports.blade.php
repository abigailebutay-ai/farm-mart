@extends('layouts.app')

@section('page-title', 'User Approvals')

@section('content')
    <x-ui.page-header
        title="User Approvals"
        subtitle="Review new farmer and consumer accounts before they can fully use the system."
    />

    <div class="grid gap-4 md:grid-cols-4">
        <x-ui.stat-card label="Farmers" :value="$totalFarmers ?? 0" icon="farmer" trend="Registered farmer sellers." />
        <x-ui.stat-card label="Consumers" :value="$totalBuyers ?? 0" icon="buyer" tone="blue" trend="Registered marketplace buyers." />
        <x-ui.stat-card label="Pending User Approvals" :value="$pendingVerifications ?? 0" icon="clock" tone="amber" trend="Users waiting for admin approval." />
        <x-ui.stat-card label="Unread Feedback" :value="$unreadFeedbackCount ?? 0" icon="star" tone="amber" trend="Buyer feedback waiting for review." />
    </div>

    <div class="mt-5">
        <x-ui.table-card title="Pending User Approvals" subtitle="Approve or reject registered farmers and consumers.">
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
                @forelse($users ?? [] as $reportedUser)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900">{{ $reportedUser->name }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $reportedUser->email }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ \Illuminate\Support\Str::title($reportedUser->role) }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ optional($reportedUser->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                        <td class="px-5 py-4">
                            <x-ui.status-badge :status="$reportedUser->verification_status ?? ($reportedUser->is_verified ? 'Approved' : 'Pending')" />
                        </td>
                        <td class="px-5 py-4">
                            @if($reportedUser->verification_status === 'pending' && in_array($reportedUser->role, ['farmer', 'consumer', 'buyer'], true))
                                <div class="flex flex-wrap items-center gap-2">
                                    <form method="POST" action="{{ route('admin.users.approve', $reportedUser) }}" class="inline" onsubmit="return confirm('Approve this user account?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100 dark:bg-emerald-900/40 dark:text-emerald-200 dark:hover:bg-emerald-900/60">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.reject', $reportedUser) }}" class="inline" onsubmit="return confirm('Reject this user account?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 transition hover:bg-red-100 dark:bg-red-900/40 dark:text-red-200 dark:hover:bg-red-900/60">Reject</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-sm text-slate-400">No action</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-5">
                            <x-ui.empty-state title="No users waiting for approval." message="New farmer and consumer registrations will appear here." icon="users" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table-card>
    </div>

    <div class="mt-5">
        <x-ui.table-card title="Buyer Feedback" subtitle="Recent feedback submitted by buyers.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Buyer</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Rating</th>
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3">Message</th>
                    <th class="px-5 py-3">Submitted</th>
                    <th class="px-5 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentFeedback ?? [] as $feedbackItem)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900">{{ $feedbackItem->user->name ?? 'Buyer' }}</p>
                            <p class="text-sm text-slate-500">{{ $feedbackItem->user->email ?? 'Email unavailable' }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $feedbackItem->feedback_type }}</td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">{{ $feedbackItem->rating }}/5</td>
                        <td class="px-5 py-4 text-sm text-slate-600">
                            @if($feedbackItem->order)
                                <a href="{{ route('orders.show', $feedbackItem->order) }}" class="font-bold text-emerald-800">#{{ $feedbackItem->order->id }}</a>
                            @else
                                No specific order
                            @endif
                        </td>
                        <td class="max-w-md px-5 py-4 text-sm leading-relaxed text-slate-600">{{ $feedbackItem->message }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ optional($feedbackItem->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('admin.feedback.destroy', $feedbackItem) }}" onsubmit="return confirm('Delete this feedback? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-700 transition hover:bg-red-50 dark:border-red-900/60 dark:bg-gray-950 dark:text-red-300 dark:hover:bg-red-950/30">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-5">
                            <x-ui.empty-state title="No buyer feedback yet." message="Buyer feedback will appear here after submission." icon="star" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table-card>
    </div>
@endsection
