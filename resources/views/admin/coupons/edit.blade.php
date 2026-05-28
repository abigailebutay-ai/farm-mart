@extends('layouts.app')

@section('page-title', 'Edit Coupon')

@section('content')
    <x-ui.page-header
        title="Edit Coupon"
        subtitle="Update coupon rules, discount value, or active status."
    />

    <x-ui.dashboard-card class="max-w-3xl" title="Coupon Details" subtitle="Kilogram coupons only count products sold per kg.">
        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
            @method('PUT')
            @include('admin.coupons._form', [
                'coupon' => $coupon,
                'submitLabel' => 'Save Changes',
            ])
        </form>
    </x-ui.dashboard-card>
@endsection
