@extends('layouts.app')

@section('page-title', 'Checkout')

@section('content')
    <x-ui.page-header
        title="Checkout"
        subtitle="Confirm delivery details, choose a payment method, and place your order."
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Checkout Details</h2>
                </div>

                <form method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data" class="p-6 space-y-6" x-data="{ paymentMethod: @js(old('payment_method', 'cod')) }">
                    @csrf

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Order Items</h3>
                        <div class="space-y-4">
                            @foreach($cart->items as $item)
                                <div class="flex items-center justify-between gap-4 rounded-lg bg-gray-100 p-4 dark:bg-gray-700">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $item->product->name }}</p>
                                        <p class="text-base text-gray-600 dark:text-gray-400">Qty: {{ $item->quantity }} {{ $item->product->unit ?? 'piece' }} x PHP {{ number_format($item->price, 2) }} / {{ $item->product->unit ?? 'piece' }}</p>
                                    </div>
                                    <p class="font-semibold text-gray-900 dark:text-white">PHP {{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-base font-semibold text-gray-900 dark:text-white mb-2">Special Instructions (Optional)</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Any delivery instructions?">{{ old('notes') }}</textarea>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Payment Method</h3>
                        <div class="grid gap-3 md:grid-cols-2">
                            <label class="cursor-pointer rounded-xl border p-4 transition dark:border-gray-700 dark:bg-gray-900" :class="paymentMethod === 'cod' ? 'border-emerald-500 ring-2 ring-emerald-500/30' : 'border-gray-300'">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="payment_method" value="cod" x-model="paymentMethod" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">Cash on Delivery</p>
                                        <p class="mt-1 text-base text-gray-600 dark:text-gray-400">Pay when your order is received.</p>
                                    </div>
                                </div>
                            </label>

                            <label class="cursor-pointer rounded-xl border p-4 transition dark:border-gray-700 dark:bg-gray-900" :class="paymentMethod === 'gcash' ? 'border-emerald-500 ring-2 ring-emerald-500/30' : 'border-gray-300'">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="payment_method" value="gcash" x-model="paymentMethod" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">GCash</p>
                                        <p class="mt-1 text-base text-gray-600 dark:text-gray-400">Upload proof of payment before placing your order.</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('payment_method')
                            <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                        @enderror

                        <div x-cloak x-show="paymentMethod === 'gcash'" x-transition class="mt-4 rounded-xl border border-emerald-900/50 bg-gray-900 p-4">
                            <h4 class="font-bold text-white">GCash Payment Instructions</h4>
                            <p class="mt-2 text-base leading-relaxed text-gray-300">Upload proof of payment before placing your order.</p>
                            <p class="mt-2 text-base font-bold text-emerald-300">Amount to pay: PHP {{ number_format($checkoutTotal ?? $cart->total, 2) }}</p>

                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="payment_reference" class="block text-base font-semibold text-gray-900 dark:text-white mb-2">GCash Reference Number</label>
                                    <input type="text" id="payment_reference" name="payment_reference" value="{{ old('payment_reference') }}" inputmode="numeric" pattern="[0-9]{11}" minlength="11" maxlength="11" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Enter 11-digit GCash reference number">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">GCash reference number must be exactly 11 digits.</p>
                                    @error('payment_reference')
                                        <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="payment_proof" class="block text-base font-semibold text-gray-900 dark:text-white mb-2">Proof of Payment</label>
                                    <input type="file" id="payment_proof" name="payment_proof" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-700 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Accepted files: JPG, PNG, WebP, or PDF up to 5 MB.</p>
                                    @error('payment_proof')
                                        <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900">
                        <h3 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">Delivery Details</h3>
                        <p class="text-base text-blue-800 dark:text-blue-300">
                            <strong>Name:</strong> {{ auth()->user()->name }}<br>
                            <strong>Address:</strong> {{ auth()->user()->address ?? 'Not provided' }}<br>
                            <strong>Phone:</strong> {{ auth()->user()->phone ?? 'Not provided' }}
                        </p>
                        <p class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                            <span class="font-semibold">Use the account details saved during registration.</span>
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 rounded-lg bg-green-600 px-6 py-3 font-semibold text-white transition hover:bg-green-700">
                            Place Order
                        </button>
                        <a href="{{ route('cart.index') }}" class="flex-1 rounded-lg bg-gray-300 px-6 py-3 text-center font-semibold text-gray-900 transition hover:bg-gray-400 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                            Back to Cart
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden sticky top-24">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Order Summary</h2>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Total Items:</span>
                        <span>{{ $cart->items->sum('quantity') }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span>PHP {{ number_format($cart->subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Total kg:</span>
                        <span>{{ number_format($totalKg ?? 0, 2) }} kg</span>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <h3 class="font-bold text-gray-900 dark:text-white">Coupon</h3>
                        @if($coupon ?? null)
                            <div class="mt-3 flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-emerald-700 dark:text-emerald-300">{{ $coupon->code }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Discount: PHP {{ number_format($discountAmount ?? 0, 2) }}</p>
                                </div>
                                <form method="POST" action="{{ route('checkout.coupon.remove') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-50 dark:border-red-900/60 dark:text-red-300 dark:hover:bg-red-950/30">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('checkout.coupon.apply') }}" class="mt-3 flex gap-2">
                                @csrf
                                <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" class="min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Enter coupon code">
                                <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Apply</button>
                            </form>
                        @endif
                    </div>

                    @if(($discountAmount ?? 0) > 0)
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                            <span>Discount:</span>
                            <span>- PHP {{ number_format($discountAmount, 2) }}</span>
                        </div>
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white">
                            <span>Total to Pay:</span>
                            <span class="text-green-600 dark:text-green-400">PHP {{ number_format($checkoutTotal ?? $cart->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('input', function (event) {
            if (event.target.name === 'payment_reference') {
                event.target.value = event.target.value.replace(/\D/g, '').slice(0, 11);
            }
        });
    </script>
@endsection
