@extends('layouts.app')

@section('page-title', 'Create Announcement')

@section('content')
    <x-ui.page-header
        title="Create Announcement"
        subtitle="Publish a platform update for SariwaLink users."
    />

    <x-ui.dashboard-card class="max-w-3xl" title="Announcement Details" subtitle="Published announcements can be shown to users outside the admin area.">
        <form method="POST" action="{{ route('admin.announcements.store') }}">
            @include('admin.announcements._form', [
                'announcement' => $announcement,
                'submitLabel' => 'Save Announcement',
            ])
        </form>
    </x-ui.dashboard-card>
@endsection
