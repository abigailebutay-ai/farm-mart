@extends('layouts.app')

@section('page-title', 'Activity Logs')

@section('content')
    <x-ui.page-header
        title="Activity Logs"
        subtitle="Review recent farmer, buyer, product, and order activity across the platform."
    />

    <x-ui.dashboard-card title="Activity Logs" subtitle="Recent marketplace activity for admin monitoring.">
        <div class="space-y-3">
            @forelse($activityLogs ?? [] as $activity)
                <div class="flex gap-3 rounded-xl border border-slate-100 p-3 dark:border-gray-800">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                        <x-ui.icon :name="$activity['icon'] ?? 'activity'" />
                    </span>
                    <div class="min-w-0">
                        <p class="font-bold text-slate-900 dark:text-white">{{ $activity['title'] ?? 'System activity' }}</p>
                        <p class="text-sm text-slate-500 dark:text-gray-400">{{ $activity['description'] ?? 'Activity details unavailable.' }}</p>
                        <p class="mt-1 text-xs font-medium text-amber-600">{{ optional($activity['date'] ?? null)->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <x-ui.empty-state title="No activity logs yet" message="Registrations, product listings, and order activity will appear here." icon="activity" />
            @endforelse
        </div>
    </x-ui.dashboard-card>
@endsection
