@extends('layouts.app')

@section('page-title', 'Edit Announcement')

@section('content')
    <x-ui.page-header
        title="Edit Announcement"
        subtitle="Update the announcement message or status."
    />

    <x-ui.dashboard-card class="max-w-3xl" title="Announcement Details" subtitle="Keep published updates clear and current for users.">
        <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
            @method('PUT')
            @include('admin.announcements._form', [
                'announcement' => $announcement,
                'submitLabel' => 'Save Changes',
            ])
        </form>
    </x-ui.dashboard-card>
@endsection
