@extends('layouts.app')

@section('page-title', 'Create Coupon')

@section('content')
    <x-ui.page-header
        title="Create Coupon"
        subtitle="Set up a discount based on order amount or total kilograms."
    />

    <x-ui.dashboard-card class="max-w-3xl" title="Coupon Details" subtitle="Kilogram coupons only count products sold per kg.">
        <form method="POST" action="{{ route('admin.coupons.store') }}">
            @include('admin.coupons._form', [
                'coupon' => $coupon,
                'submitLabel' => 'Create Coupon',
            ])
        </form>
    </x-ui.dashboard-card>
@endsection
