@extends('layouts.app')

@section('page-title', 'Receipt #' . $order->id)

@section('content')
    @php
        $reference = 'FM-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
        $farmerNames = $order->items
            ->map(fn ($item) => $item->farmer->name ?? null)
            ->filter()
            ->unique()
            ->values();
        $orderedAt = $order->created_at->timezone(config('app.timezone'))->format('M d, Y h:i A');
    @endphp

    <div class="no-print mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Receipt #{{ $order->id }}</h1>
            <p class="receipt-meta mt-1 text-sm">Print this receipt or save it as a PDF from your browser print dialog.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-ui.secondary-button href="{{ route('orders.index') }}">Back to Orders</x-ui.secondary-button>
            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800">
                Print / Save as PDF
            </button>
        </div>
    </div>

    <section class="receipt-container print-container mx-auto max-w-4xl rounded-2xl border p-6 shadow-sm md:p-8">
        <div class="print-header flex flex-col gap-4 border-b border-slate-200 pb-6 md:flex-row md:items-start md:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-700 text-white">
                        <x-ui.icon name="farmer" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="print-brand text-xl font-black">SariwaLink</p>
                        <p class="print-subtitle receipt-meta text-sm">FARM-TO-MARKET PLATFORM</p>
                    </div>
                </div>
                <h2 class="print-title mt-6 text-3xl font-black">Official Receipt</h2>
                <p class="receipt-meta mt-1 text-sm">Thank you for supporting local farmers.</p>
            </div>
            <div class="text-left md:text-right">
                <p class="receipt-meta text-xs font-bold uppercase tracking-wide">Reference Number</p>
                <p class="text-lg font-black">{{ $reference }}</p>
                <p class="receipt-meta mt-3 text-xs font-bold uppercase tracking-wide">Payment Method</p>
                <p class="font-semibold">{{ $order->paymentMethodLabel() }}</p>
                <p class="receipt-meta mt-3 text-xs font-bold uppercase tracking-wide">Payment Status</p>
                <p class="font-semibold">{{ $order->paymentStatusLabel() }}</p>
                @if($order->payment_method === 'gcash' && $order->payment_status !== 'paid')
                    <p class="mt-1 text-sm font-semibold text-amber-700">Payment not yet verified.</p>
                @endif
                @if($order->payment_reference)
                    <p class="receipt-meta mt-3 text-xs font-bold uppercase tracking-wide">GCash Reference Number</p>
                    <p class="font-semibold">{{ $order->payment_reference }}</p>
                @endif
                @if($order->payment_proof)
                    <p class="receipt-meta mt-3 text-xs font-bold uppercase tracking-wide">Proof of Payment</p>
                    <a href="{{ $order->paymentProofUrl() }}" target="_blank" rel="noopener" class="font-semibold text-emerald-700">View Proof</a>
                @endif
            </div>
        </div>

        <div class="grid gap-5 border-b border-slate-200 py-6 md:grid-cols-2">
            <div>
                <p class="receipt-meta text-xs font-bold uppercase tracking-wide">Buyer</p>
                <p class="mt-1 font-bold">{{ $order->consumer->name }}</p>
                <p class="receipt-meta text-sm">{{ $order->consumer->email }}</p>
            </div>
            <div>
                <p class="receipt-meta text-xs font-bold uppercase tracking-wide">Order Details</p>
                <p class="mt-1 text-sm"><span class="font-semibold">Order ID:</span> #{{ $order->id }}</p>
                <p class="text-sm"><span class="font-semibold">Ordered At:</span> {{ $orderedAt }}</p>
                <p class="text-sm"><span class="font-semibold">Status:</span> {{ \Illuminate\Support\Str::title($order->status) }}</p>
                <p class="text-sm"><span class="font-semibold">Seller:</span> {{ $farmerNames->isNotEmpty() ? $farmerNames->join(', ') : 'Local Farmer' }}</p>
            </div>
        </div>

        <div class="overflow-x-auto py-6">
            <table class="receipt-table w-full border-collapse text-left text-sm">
                <thead>
                    <tr>
                        <th class="border-b px-3 py-3 font-bold">Product</th>
                        <th class="border-b px-3 py-3 font-bold">Farmer</th>
                        <th class="border-b px-3 py-3 font-bold">Quantity</th>
                        <th class="border-b px-3 py-3 font-bold">Price / Unit</th>
                        <th class="border-b px-3 py-3 text-right font-bold">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        @php($unit = optional($item->product)->unit ?? 'piece')
                        <tr>
                            <td class="border-b px-3 py-3 font-semibold">{{ optional($item->product)->name ?? 'Product unavailable' }}</td>
                            <td class="border-b px-3 py-3">{{ $item->farmer->name ?? 'Local Farmer' }}</td>
                            <td class="border-b px-3 py-3">{{ $item->quantity }} {{ $unit }}</td>
                            <td class="border-b px-3 py-3">PHP {{ number_format($item->price, 2) }} / {{ $unit }}</td>
                            <td class="border-b px-3 py-3 text-right font-semibold">PHP {{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="ml-auto max-w-sm space-y-3 border-t border-slate-200 pt-5">
            <div class="flex justify-between receipt-meta">
                <span>Subtotal</span>
                <span>PHP {{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="print-total-row flex justify-between text-xl font-black">
                <span>Total Amount</span>
                <span>PHP {{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <div class="print-footer receipt-meta mt-8 border-t border-slate-200 pt-5 text-center text-sm">
            <p>Prepared by SariwaLink System</p>
            <p>Thank you for supporting local farmers.</p>
        </div>
    </section>
@endsection
