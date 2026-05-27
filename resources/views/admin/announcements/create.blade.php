@extends('layouts.app')

@section('page-title', 'Create Announcement')

@section('content')
    <x-ui.page-header
        title="Create Announcement"
        subtitle="Write a clear message for users."
    />

    <x-ui.dashboard-card class="max-w-3xl" title="Announcement Details" subtitle="Published announcements can be shown to users in the system.">
        <form method="POST" action="{{ route('admin.announcements.store') }}">
            @include('admin.announcements._form', [
                'announcement' => $announcement,
                'submitLabel' => 'Publish Announcement',
            ])
        </form>
    </x-ui.dashboard-card>
@endsection
