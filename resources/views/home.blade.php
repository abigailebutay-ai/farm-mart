@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <main class="bg-stone-50">
        <section class="border-b border-emerald-100 bg-gradient-to-br from-stone-50 via-white to-emerald-50">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:py-14 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
                <div class="flex flex-col justify-center">
                    <div class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-100 bg-white px-3 py-1.5 text-xs font-black uppercase tracking-wide text-emerald-800 shadow-sm">
                        <x-ui.icon name="buyer" class="h-4 w-4" />
                        Local produce marketplace
                    </div>

                    <h1 class="mt-5 max-w-3xl text-4xl font-black leading-tight tracking-tight text-slate-950 md:text-5xl">
                        From Local Farms to Your Basket — Smarter, Faster, Fairer.
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600 md:text-lg">
                        Farm-Mart helps farmers sell fresh products directly to buyers while managing inventory, orders, sales, and decision-support insights in one platform.
                    </p>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <x-ui.primary-button href="{{ route('marketplace') }}" class="px-5 py-2.5">Shop Marketplace</x-ui.primary-button>
                        <x-ui.secondary-button href="{{ route('register') }}" class="px-5 py-2.5">Start Selling</x-ui.secondary-button>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach([
                            ['Fresh Local Produce', 'products'],
                            ['Direct Farmer-to-Buyer', 'buyer'],
                            ['Inventory Alerts', 'alert'],
                            ['Order Tracking', 'orders'],
                        ] as [$label, $icon])
                            <span class="inline-flex items-center gap-2 rounded-full border border-slate-100 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm">
                                <x-ui.icon :name="$icon" class="h-3.5 w-3.5 text-emerald-700" />
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="lg:pl-4">
                    <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-xl shadow-emerald-900/5 dark:border-slate-700 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3 dark:border-slate-700">
                            <div>
                                <p class="text-sm font-black text-slate-900 dark:text-slate-100">Market Preview</p>
                                <p class="text-xs text-slate-500 dark:text-slate-300">Products, orders, and supply signals</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">Today</span>
                        </div>

                        @php
                            $previewProduct = $featuredProducts->first();
                        @endphp
                        <div class="mt-4">
                            @if($previewProduct)
                                @php
                                    $previewStockStatus = $previewProduct->quantity > 10 ? 'In Stock' : ($previewProduct->quantity > 0 ? 'Low Stock' : 'Out of Stock');
                                @endphp
                                <div class="rounded-xl border border-slate-100 bg-stone-50 p-3 dark:border-slate-700 dark:bg-slate-800">
                                    <div class="flex items-center gap-3">
                                        <x-ui.product-image
                                            :product="$previewProduct"
                                            :alt="$previewProduct->name"
                                            image-class="h-20 w-20 shrink-0 rounded-xl object-cover"
                                            placeholder-class="flex h-20 w-20 shrink-0 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-900/30 dark:text-emerald-200"
                                            icon-class="h-8 w-8"
                                        />
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="min-w-0">
                                                    <p class="truncate text-base font-black text-slate-900 dark:text-slate-100">{{ $previewProduct->name }}</p>
                                                    <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-300">{{ optional($previewProduct->farmer)->name ?? 'Local Farmer' }}</p>
                                                </div>
                                                <x-ui.status-badge :status="$previewStockStatus" />
                                            </div>
                                            <p class="mt-2 text-sm font-black text-emerald-800 dark:text-emerald-200">PHP {{ number_format($previewProduct->price, 2) }} / {{ $previewProduct->unit ?? 'piece' }}</p>
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">{{ $previewProduct->quantity }} {{ $previewProduct->unit ?? 'piece' }} available</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-xl border border-slate-100 bg-stone-50 p-3 dark:border-slate-700 dark:bg-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-900/30 dark:text-emerald-200">
                                            <x-ui.icon name="products" class="h-8 w-8" />
                                        </div>
                                        <div>
                                            <p class="text-base font-black text-slate-900 dark:text-slate-100">Product preview</p>
                                            <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-300">Listings appear when farmers add products.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl border border-amber-100 bg-amber-50/80 p-3 dark:border-amber-900/50 dark:bg-slate-800">
                                <p class="text-xs font-bold text-amber-700 dark:text-amber-300">Low Stock</p>
                                <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">{{ $lowStockCount ?? 0 }}</p>
                                <p class="mt-1 text-[11px] font-semibold text-slate-500 dark:text-slate-300">Products under threshold</p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50/80 p-3 dark:border-emerald-900/50 dark:bg-slate-800">
                                <p class="text-xs font-bold text-emerald-700 dark:text-emerald-300">Monthly Sales</p>
                                <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">PHP {{ number_format($monthlySalesTotal ?? 0, 2) }}</p>
                                <p class="mt-1 text-[11px] font-semibold text-slate-500 dark:text-slate-300">{{ $monthlyCompletedOrdersCount ?? 0 }} completed orders this month</p>
                            </div>
                            <div class="rounded-xl border border-lime-100 bg-lime-50/80 p-3 dark:border-lime-900/40 dark:bg-slate-800">
                                <p class="text-xs font-bold text-lime-700 dark:text-lime-300">Restock Needed</p>
                                <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">{{ $restockNeededCount ?? 0 }} products</p>
                                <p class="mt-1 text-[11px] font-semibold text-slate-500 dark:text-slate-300">Low or out of stock</p>
                            </div>
                        </div>

                        @php
                            $recentOrderItemCount = $recentOrder ? $recentOrder->items->count() : 0;
                            $recentOrderDescription = $recentOrder
                                ? $recentOrderItemCount . ' item' . ($recentOrderItemCount === 1 ? '' : 's') . ' - PHP ' . number_format($recentOrder->total, 2)
                                : 'New orders will appear once buyers check out.';
                        @endphp
                        <div class="mt-4 flex items-center justify-between gap-3 rounded-xl border border-slate-100 p-3 dark:border-slate-700 dark:bg-slate-800">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-black text-slate-900 dark:text-slate-100">{{ $recentOrder ? 'Order #' . $recentOrder->id : 'Recent Order' }}</p>
                                <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-300">{{ $recentOrderDescription }}</p>
                                @if($recentOrder)
                                    <p class="mt-1 text-[11px] font-semibold text-slate-400 dark:text-slate-400">{{ optional($recentOrder->consumer)->name ?? 'Buyer' }} - {{ $recentOrder->created_at->timezone(config('app.timezone'))->format('M d, Y') }}</p>
                                @endif
                            </div>
                            <div class="shrink-0">
                                <x-ui.status-badge :status="$recentOrder->status ?? 'No Orders'" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if(($publishedAnnouncements ?? collect())->isNotEmpty())
            <section class="border-y border-emerald-100 bg-white dark:border-gray-800 dark:bg-gray-950">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="mb-5">
                        <p class="text-xs font-black uppercase tracking-wide text-amber-600">Announcements</p>
                        <h2 class="mt-1 text-2xl font-black text-slate-900 dark:text-white">Latest Farm-Mart Updates</h2>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($publishedAnnouncements as $announcement)
                            <article class="rounded-2xl border border-slate-100 bg-stone-50/80 p-4 dark:border-gray-800 dark:bg-gray-900">
                                <p class="text-sm font-black text-slate-900 dark:text-white">{{ $announcement->title }}</p>
                                <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($announcement->body, 140) }}</p>
                                <p class="mt-3 text-xs font-semibold text-emerald-700 dark:text-emerald-300">{{ optional($announcement->published_at)->timezone(config('app.timezone'))->format('M d, Y') }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-amber-600">Marketplace</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-900 md:text-3xl">Fresh Products from Local Farmers</h2>
                    <p class="mt-2 max-w-2xl text-sm text-slate-600">Browse fresh listings and connect directly with farmer sellers.</p>
                </div>
                <x-ui.secondary-button href="{{ route('marketplace') }}">View All</x-ui.secondary-button>
            </div>

            @if($featuredProducts->count() > 0)
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($featuredProducts->take(4) as $product)
                        <x-ui.product-card :product="$product" compact />
                    @endforeach
                </div>
            @else
                <x-ui.empty-state title="No approved products yet" message="Farmer listings will appear here once available." icon="products" />
            @endif
        </section>

        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <p class="text-xs font-black uppercase tracking-wide text-amber-600">Platform Tools</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-900 md:text-3xl">Built for Farmers, Buyers, and Local Supply Chains</h2>
                </div>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach([
                        ['Product Storefronts', 'Create clear farm product listings.', 'products'],
                        ['Inventory Monitoring', 'Track stock and low-stock alerts.', 'inventory'],
                        ['Order Management', 'Manage buyer orders and fulfillment.', 'orders'],
                        ['Sales Reports', 'View sales and income summaries.', 'report'],
                        ['Decision Support', 'Spot demand and restock signals.', 'chart'],
                        ['Admin Verification', 'Build trust through monitored accounts.', 'check'],
                    ] as [$title, $copy, $icon])
                        <div class="rounded-2xl border border-slate-100 bg-stone-50/80 p-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800">
                                <x-ui.icon :name="$icon" class="h-5 w-5" />
                            </span>
                            <h3 class="mt-3 font-black text-slate-900">{{ $title }}</h3>
                            <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $copy }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-amber-600">Workflow</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-900 md:text-3xl">How It Works</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">A simple farm-to-market process for product listing, buyer ordering, fulfillment, and monitoring.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach([
                        ['1', 'Farmers list fresh products'],
                        ['2', 'Buyers browse and order'],
                        ['3', 'Farmers prepare pickup or delivery'],
                        ['4', 'Admin verifies and monitors'],
                    ] as [$number, $step])
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-sm font-black text-white">{{ $number }}</span>
                            <p class="text-sm font-bold text-slate-800">{{ $step }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-emerald-50/60">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-6 max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-wide text-amber-600">Decision Support</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-900 md:text-3xl">Decision Support for Smarter Selling</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">These insights help farmers decide what to restock, what products are in demand, and how their sales are performing.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach([
                        ['Best-selling products', 'star'],
                        ['Low-stock alerts', 'alert'],
                        ['High-demand products', 'chart'],
                        ['Products with no orders', 'products'],
                        ['Monthly sales summary', 'money'],
                        ['Suggested restock quantity', 'inventory'],
                    ] as [$title, $icon])
                        <div class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm">
                            <x-ui.icon :name="$icon" class="h-5 w-5 text-emerald-700" />
                            <p class="mt-3 text-sm font-black text-slate-900">{{ $title }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-5 lg:grid-cols-3">
                @foreach([
                    ['Farmer', ['Add products', 'Update inventory', 'Manage orders', 'View insights'], 'farmer', route('register')],
                    ['Buyer', ['Browse fresh products', 'Add to cart', 'Track orders', 'Review purchases'], 'buyer', route('marketplace')],
                    ['Admin', ['Verify accounts', 'Monitor products', 'Track orders', 'Review activity'], 'dashboard', '#'],
                ] as [$role, $items, $icon, $href])
                    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800">
                            <x-ui.icon :name="$icon" />
                        </span>
                        <h3 class="mt-4 text-xl font-black text-slate-900">{{ $role }}</h3>
                        <ul class="mt-3 space-y-2">
                            @foreach($items as $item)
                                <li class="flex items-center gap-2 text-sm text-slate-600">
                                    <x-ui.icon name="check" class="h-3.5 w-3.5 text-emerald-700" />
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ $href }}" class="mt-4 inline-flex rounded-xl border border-emerald-200 px-3 py-2 text-sm font-bold text-emerald-800 hover:bg-emerald-50">Open</a>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900">Ready to connect farms and buyers?</h2>
                        <p class="mt-1 text-sm text-slate-600">Start browsing local produce or create an account for Farm-Mart.</p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <x-ui.secondary-button href="{{ route('marketplace') }}">Browse Marketplace</x-ui.secondary-button>
                        <x-ui.primary-button href="{{ route('register') }}">Create Account</x-ui.primary-button>
                    </div>
                </div>
            </div>
        </section>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto grid max-w-7xl gap-3 px-4 py-7 text-sm text-slate-500 sm:px-6 md:grid-cols-[1fr_auto] lg:px-8">
                <div>
                    <p class="font-black text-emerald-900">Farm-Mart</p>
                    <p>Farmers Digital Supply Chain with Decision Support</p>
                    <p>Capstone Project - St. Paul University Philippines</p>
                </div>
                <p class="font-semibold">© 2026 Farm-Mart</p>
            </div>
        </footer>
    </main>
@endsection
