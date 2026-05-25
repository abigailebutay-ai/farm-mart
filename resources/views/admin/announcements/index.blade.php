@extends('layouts.app')

@section('page-title', 'Announcements')

@section('content')
    <x-ui.page-header
        title="Announcements"
        subtitle="Prepare and monitor platform announcements for Farm-Mart users."
    />

    <x-ui.dashboard-card title="Announcements" subtitle="Published platform updates will appear here.">
        <div class="mb-4 flex justify-end">
            <x-ui.primary-button href="{{ route('admin.announcements.create') }}" class="gap-2">
                <x-ui.icon name="megaphone" class="h-4 w-4" />
                Create Announcement
            </x-ui.primary-button>
        </div>

        @if($announcements->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3">Announcement</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Published</th>
                            <th class="px-5 py-3">Created By</th>
                            <th class="px-5 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800">
                        @foreach($announcements as $announcement)
                            <tr class="hover:bg-slate-50 dark:hover:bg-gray-900">
                                <td class="px-5 py-4">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $announcement->title }}</p>
                                    <p class="mt-1 max-w-xl text-sm leading-relaxed text-slate-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($announcement->body, 120) }}</p>
                                </td>
                                <td class="px-5 py-4"><x-ui.status-badge :status="$announcement->status" /></td>
                                <td class="px-5 py-4 text-sm text-slate-500 dark:text-gray-400">
                                    {{ optional($announcement->published_at)->format('M d, Y h:i A') ?? 'Not published' }}
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600 dark:text-gray-300">
                                    {{ $announcement->creator->name ?? 'Admin' }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <x-ui.secondary-button href="{{ route('admin.announcements.edit', $announcement) }}" class="px-3 py-1.5 text-xs">Edit</x-ui.secondary-button>
                                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50 dark:border-red-900/60 dark:bg-gray-950 dark:text-red-300 dark:hover:bg-red-950/30">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $announcements->links() }}
            </div>
        @else
            <x-ui.empty-state title="No announcements yet" message="Create an announcement when you are ready to publish a platform update." icon="megaphone" />
        @endif
    </x-ui.dashboard-card>
@endsection
