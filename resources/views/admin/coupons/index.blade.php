@extends('layouts.app')

@section('page-title', 'Coupons')

@section('content')
    <x-ui.page-header
        title="Coupons"
        subtitle="Create discounts based on order amount or total kilograms ordered."
    />

    <x-ui.dashboard-card title="Coupons" subtitle="Manage checkout coupon codes.">
        <div class="mb-4 flex justify-end">
            <x-ui.primary-button href="{{ route('admin.coupons.create') }}">Create Coupon</x-ui.primary-button>
        </div>

        @if($coupons->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3">Code</th>
                            <th class="px-5 py-3">Rule</th>
                            <th class="px-5 py-3">Discount</th>
                            <th class="px-5 py-3">Usage</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800">
                        @foreach($coupons as $coupon)
                            <tr class="hover:bg-slate-50 dark:hover:bg-gray-900">
                                <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">{{ $coupon->code }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600 dark:text-gray-300">
                                    @if($coupon->rule_type === 'kilogram')
                                        Total Kilograms, minimum {{ number_format($coupon->minimum_kg, 2) }} kg
                                    @else
                                        Order Amount, minimum PHP {{ number_format($coupon->minimum_order_amount ?? 0, 2) }}
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ $coupon->type === 'percent' ? number_format($coupon->value, 2) . '%' : 'PHP ' . number_format($coupon->value, 2) }}
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600 dark:text-gray-300">
                                    {{ $coupon->used_count }}{{ $coupon->usage_limit ? ' / ' . $coupon->usage_limit : '' }}
                                </td>
                                <td class="px-5 py-4"><x-ui.status-badge :status="$coupon->isAvailable() ? 'Active' : 'Inactive'" /></td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <x-ui.secondary-button href="{{ route('admin.coupons.edit', $coupon) }}" class="px-3 py-1.5 text-xs">Edit</x-ui.secondary-button>
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete this coupon?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-50 dark:border-red-900/60 dark:bg-gray-950 dark:text-red-300 dark:hover:bg-red-950/30">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">{{ $coupons->links() }}</div>
        @else
            <x-ui.empty-state title="No coupons yet." message="Create coupon codes for order amount or kilogram-based discounts." icon="money" />
        @endif
    </x-ui.dashboard-card>
@endsection
