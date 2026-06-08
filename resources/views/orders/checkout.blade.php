@extends('layouts.app')

@section('page-title', 'Checkout')

@section('content')
    @php($selectedFarmerName = html_entity_decode($selectedFarmer->name, ENT_QUOTES | ENT_HTML5, 'UTF-8'))

    <x-ui.page-header
        title="Checkout"
        :subtitle="'You are ordering from ' . $selectedFarmerName . '.'"
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Checkout Details</h2>
                </div>

                <form id="checkout-form" method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data" class="p-6 space-y-6" x-data="{ paymentMethod: @js(old('payment_method', 'cod')), fulfillmentMethod: @js(old('fulfillment_method', 'delivery')), purchaseType: @js(old('purchase_type', $purchaseType ?? 'home')), checkoutSubtotal: @js((float) ($checkoutSubtotal ?? $checkoutItems->sum('subtotal'))), checkoutTotal: @js((float) ($checkoutTotal ?? $checkoutItems->sum('subtotal'))), money(value) { return 'PHP ' + Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) } }">
                    @csrf
                    <input type="hidden" name="farmer_id" value="{{ $selectedFarmer->id }}">

                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100">
                        You are ordering from {{ $selectedFarmer->name }}. Discounts, GCash details, and pickup location are based on this farmer only.
                    </div>

                    <div>
                        <h3 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Purchase Type</h3>
                        <div class="grid gap-3 md:grid-cols-2">
                            <label class="cursor-pointer rounded-xl border p-4 transition dark:border-gray-700 dark:bg-gray-900" :class="purchaseType === 'home' ? 'border-emerald-500 ring-2 ring-emerald-500/30' : 'border-gray-300'">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="purchase_type" value="home" x-model="purchaseType" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">For Home Use</p>
                                        <p class="mt-1 text-base text-gray-600 dark:text-gray-400">For regular household consumption.</p>
                                    </div>
                                </div>
                            </label>

                            <label class="cursor-pointer rounded-xl border p-4 transition dark:border-gray-700 dark:bg-gray-900" :class="purchaseType === 'bulk' ? 'border-emerald-500 ring-2 ring-emerald-500/30' : 'border-gray-300'">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="purchase_type" value="bulk" x-model="purchaseType" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">Bulk Order</p>
                                        <p class="mt-1 text-base text-gray-600 dark:text-gray-400">For larger purchases that may qualify for discount.</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('purchase_type')
                            <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Order Items</h3>
                        <div class="space-y-4">
                            @foreach($checkoutItems as $item)
                                <div class="flex flex-col gap-4 rounded-lg bg-gray-100 p-4 dark:bg-gray-700 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex gap-4">
                                        <x-ui.product-image
                                            :product="$item->product"
                                            :alt="$item->product->name"
                                            image-class="h-16 w-16 rounded-lg object-cover"
                                            placeholder-class="flex h-16 w-16 items-center justify-center rounded-lg bg-gray-200 text-gray-400 dark:bg-gray-800"
                                            icon-class="h-6 w-6"
                                        />
                                        <div>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $item->product->name }}</p>
                                            <p class="text-base text-gray-600 dark:text-gray-400">Quantity: {{ $item->quantity }} kg</p>
                                            <p class="text-base text-gray-600 dark:text-gray-400">Price: PHP {{ number_format($item->price, 2) }} / kg</p>
                                        </div>
                                    </div>
                                    <p class="text-lg font-bold text-green-600 dark:text-green-400">PHP {{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-base font-semibold text-gray-900 dark:text-white mb-2">Special Instructions (Optional)</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Any pickup or delivery instructions?">{{ old('notes') }}</textarea>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Fulfillment Option</h3>
                        <div class="grid gap-3 md:grid-cols-2">
                            <label class="cursor-pointer rounded-xl border p-4 transition dark:border-gray-700 dark:bg-gray-900" :class="fulfillmentMethod === 'pickup' ? 'border-emerald-500 ring-2 ring-emerald-500/30' : 'border-gray-300'">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="fulfillment_method" value="pickup" x-model="fulfillmentMethod" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">Pick up</p>
                                        <p class="mt-1 text-base text-gray-600 dark:text-gray-400">I will pick up the order from the farmer or agreed pickup location.</p>
                                    </div>
                                </div>
                            </label>

                            <label class="cursor-pointer rounded-xl border p-4 transition dark:border-gray-700 dark:bg-gray-900" :class="fulfillmentMethod === 'delivery' ? 'border-emerald-500 ring-2 ring-emerald-500/30' : 'border-gray-300'">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="fulfillment_method" value="delivery" x-model="fulfillmentMethod" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">Delivery</p>
                                        <p class="mt-1 text-base text-gray-600 dark:text-gray-400">I want the order delivered to my address.</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('fulfillment_method')
                            <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                        @enderror

                        <div x-show="fulfillmentMethod === 'delivery'" class="mt-4 rounded-lg bg-blue-50 p-4 dark:bg-blue-900">
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

                        <div x-show="fulfillmentMethod === 'pickup'" class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100">
                            <h4 class="text-base font-bold">Pickup Information</h4>
                            <p class="mt-1 font-semibold">Pickup location is based on this farmer's address.</p>

                            <div class="mt-4 space-y-3">
                                @forelse($pickupLocations as $location)
                                    @php($farmer = $location['farmer'])
                                    <div class="rounded-lg border border-amber-200/80 bg-white/70 p-3 dark:border-amber-800/80 dark:bg-gray-900/60">
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $loop->iteration }}. {{ $farmer->name }}</p>
                                        <p class="mt-1"><span class="font-semibold">Products:</span> {{ $location['products']->join(', ') }}</p>
                                        <p class="mt-1">
                                            <span class="font-semibold">Pickup Address:</span>
                                            {{ $farmer->address ?: 'Pickup address not provided. Please contact the farmer before pickup.' }}
                                        </p>
                                        <p class="mt-1"><span class="font-semibold">Seller Contact:</span> {{ $farmer->phone ?: 'Not provided' }}</p>
                                    </div>
                                @empty
                                    <div class="rounded-lg border border-amber-200 bg-white/70 p-3 font-semibold dark:border-amber-800 dark:bg-gray-900/60">
                                        Pickup address not provided. Please contact the farmer before pickup.
                                    </div>
                                @endforelse
                            </div>
                        </div>
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
                            <p class="mt-2 text-base leading-relaxed text-gray-300">Send your payment to the farmer's GCash number, then upload proof of payment.</p>
                            <p class="mt-2 text-base font-bold text-emerald-300">
                                Amount to pay:
                                <span x-text="money(purchaseType === 'bulk' ? checkoutTotal : checkoutSubtotal)">PHP {{ number_format($checkoutTotal ?? $cart->total, 2) }}</span>
                            </p>

                            @if(($cartFarmerCount ?? 0) > 1)
                                <div class="mt-4 rounded-lg border border-amber-800 bg-amber-900/30 px-4 py-3 text-sm font-semibold text-amber-100">
                                    GCash payment is only available for orders from one farmer at a time. Please check out products from one farmer only, or choose Cash on Delivery.
                                </div>
                            @elseif($gcashFarmer)
                                <div class="mt-4 rounded-lg border border-emerald-800 bg-emerald-950/40 px-4 py-3 text-sm text-emerald-100">
                                    <p class="font-bold">Farmer GCash Details</p>
                                    <p class="mt-2"><span class="font-semibold">Farmer:</span> {{ $gcashFarmer->name }}</p>
                                    <p><span class="font-semibold">GCash Name:</span> {{ $gcashFarmer->gcash_name ?: $gcashFarmer->name }}</p>
                                    <p><span class="font-semibold">GCash Number:</span> {{ $selectedFarmer->gcash_number ?: 'Not provided' }}</p>
                                    @if($gcashFarmer->gcash_qr)
                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->url($gcashFarmer->gcash_qr) }}" target="_blank" rel="noopener" class="mt-3 inline-flex rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-800">
                                            View GCash QR Code
                                        </a>
                                    @endif
                                    @if(! $gcashFarmer->gcash_number)
                                        <p class="mt-2 font-semibold text-amber-200">This farmer has not added GCash payment details. Please choose Cash on Delivery.</p>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="payment_reference" class="block text-base font-semibold text-gray-900 dark:text-white mb-2">GCash Reference Number</label>
                                    <input type="text" id="payment_reference" name="payment_reference" value="{{ old('payment_reference') }}" inputmode="numeric" pattern="[0-9]{8,20}" maxlength="20" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Enter GCash reference number">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the reference number shown on your GCash receipt.</p>
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
                        <span>Product Lines:</span>
                        <span>{{ $checkoutItems->count() }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span>PHP {{ number_format($checkoutSubtotal ?? $checkoutItems->sum('subtotal'), 2) }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Total kg:</span>
                        <span>{{ number_format($totalKg ?? 0, 2) }} kg</span>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <h3 class="font-bold text-gray-900 dark:text-white">Bulk Discount</h3>
                        @if($appliedDiscount ?? null)
                            <p x-show="purchaseType === 'home'" class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                No bulk discount applied for home-use orders.
                            </p>
                            <div x-show="purchaseType === 'bulk'" class="mt-3 flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-emerald-700 dark:text-emerald-300">Bulk discount applied</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $appliedDiscount['label'] ?? 'Bulk order discount' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Discount: PHP {{ number_format($discountAmount ?? 0, 2) }}</p>
                                </div>
                                <form method="POST" action="{{ route('cart.remove-discount') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="farmer_id" value="{{ $selectedFarmer->id }}">
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-50 dark:border-red-900/60 dark:text-red-300 dark:hover:bg-red-950/30">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        @elseif($eligibleDiscount['eligible'] ?? false)
                            <p x-show="purchaseType === 'home'" class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                No bulk discount applied for home-use orders.
                            </p>
                            <p x-show="purchaseType === 'bulk'" class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                You are eligible for a {{ $eligibleDiscount['discount_rate'] }}% bulk discount.
                            </p>
                            <form x-show="purchaseType === 'bulk'" method="POST" action="{{ route('cart.apply-discount') }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="farmer_id" value="{{ $selectedFarmer->id }}">
                                <input type="hidden" name="purchase_type" value="bulk">
                                <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Apply Discount</button>
                            </form>
                        @else
                            <p x-show="purchaseType === 'home'" class="mt-3 text-sm text-gray-500 dark:text-gray-400">No bulk discount applied for home-use orders.</p>
                            <p x-show="purchaseType === 'bulk'" class="mt-3 text-sm text-gray-500 dark:text-gray-400">Bulk discount starts at 10 kg.</p>
                        @endif
                    </div>

                    @if(($discountAmount ?? 0) > 0)
                        <div x-show="purchaseType === 'bulk'" class="flex justify-between text-emerald-600 dark:text-emerald-400">
                            <span>Discount:</span>
                            <span>- PHP {{ number_format($discountAmount, 2) }}</span>
                        </div>
                        <p x-show="purchaseType === 'home'" class="text-sm text-gray-500 dark:text-gray-400">No bulk discount applied for home-use orders.</p>
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white">
                            <span>Total to Pay:</span>
                            <span class="text-green-600 dark:text-green-400" x-text="money(purchaseType === 'bulk' ? checkoutTotal : checkoutSubtotal)">PHP {{ number_format($checkoutTotal ?? $cart->total, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" form="checkout-form" class="block w-full rounded-lg bg-green-600 py-3 text-center font-semibold text-white transition hover:bg-green-700">
                        Place Order
                    </button>

                    <a href="{{ route('cart.index') }}" class="block w-full rounded-lg bg-gray-200 py-2 text-center font-semibold text-gray-900 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                        Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('input', function (event) {
            if (event.target.name === 'payment_reference') {
                event.target.value = event.target.value.replace(/\D/g, '').slice(0, 20);
            }
        });
    </script>
@endsection
