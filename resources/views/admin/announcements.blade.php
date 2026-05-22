@extends('layouts.app')

@section('page-title', 'Announcements')

@section('content')
    <x-ui.page-header
        title="Announcements"
        subtitle="Prepare and monitor platform announcements for Farm-Mart users."
    />

    <x-ui.dashboard-card title="Announcements" subtitle="Published platform updates will appear here.">
        <div class="mb-4 flex justify-end">
            <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800">
                <x-ui.icon name="megaphone" class="h-4 w-4" />
                Create Announcement
            </button>
        </div>

        @if(($announcements ?? collect())->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3">Title</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($announcements as $announcement)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 text-sm font-bold text-slate-900">{{ $announcement['title'] ?? 'Announcement' }}</td>
                                <td class="px-5 py-4"><x-ui.status-badge :status="$announcement['status'] ?? 'Draft'" /></td>
                                <td class="px-5 py-4 text-sm text-slate-500">{{ $announcement['date'] ?? 'Not scheduled' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-ui.empty-state title="No announcements yet" message="Create an announcement when you are ready to publish a platform update." icon="megaphone" />
        @endif
    </x-ui.dashboard-card>
@endsection
