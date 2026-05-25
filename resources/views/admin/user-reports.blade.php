@extends('layouts.app')

@section('page-title', 'User Reports')

@section('content')
    <x-ui.page-header
        title="User Reports"
        subtitle="Review farmer and buyer account activity, verification status, and registration summaries."
    />

    <div class="grid gap-4 md:grid-cols-4">
        <x-ui.stat-card label="Farmers" :value="$totalFarmers ?? 0" icon="farmer" trend="Registered farmer sellers." />
        <x-ui.stat-card label="Buyers" :value="$totalBuyers ?? 0" icon="buyer" tone="blue" trend="Registered marketplace buyers." />
        <x-ui.stat-card label="Pending Reviews" :value="$pendingVerifications ?? 0" icon="clock" tone="amber" trend="Accounts waiting for verification." />
        <x-ui.stat-card label="Unread Feedback" :value="$unreadFeedbackCount ?? 0" icon="star" tone="amber" trend="Buyer feedback waiting for review." />
    </div>

    <div class="mt-5">
        <x-ui.table-card title="User Reports" subtitle="Farmer and buyer accounts available for admin review.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Role</th>
                    <th class="px-5 py-3">Verification</th>
                    <th class="px-5 py-3">Registered</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users ?? [] as $reportedUser)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900">{{ $reportedUser->name }}</p>
                            <p class="text-sm text-slate-500">{{ $reportedUser->email }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ \Illuminate\Support\Str::title($reportedUser->role) }}</td>
                        <td class="px-5 py-4">
                            <x-ui.status-badge :status="$reportedUser->verification_status ?? ($reportedUser->is_verified ? 'Approved' : 'Pending')" />
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ optional($reportedUser->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-5">
                            <x-ui.empty-state title="No user reports yet" message="Farmer and buyer account reports will appear here." icon="users" />
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
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Submitted</th>
                    <th class="px-5 py-3">Actions</th>
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
                        <td class="px-5 py-4"><x-ui.status-badge :status="$feedbackItem->status" /></td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ optional($feedbackItem->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-2">
                                @if($feedbackItem->status === 'unread')
                                    <form method="POST" action="{{ route('admin.feedback.read', $feedbackItem) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-50">Read</button>
                                    </form>
                                @endif
                                @if($feedbackItem->status !== 'resolved')
                                    <form method="POST" action="{{ route('admin.feedback.resolve', $feedbackItem) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-800">Resolve</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-5">
                            <x-ui.empty-state title="No feedback submitted yet" message="Buyer feedback will appear here after submission." icon="star" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table-card>
    </div>
@endsection
