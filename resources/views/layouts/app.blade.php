<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SariwaLink') - SariwaLink</title>
    <script>
        window.farmMartPublicShell = function () {
            return {
                publicMenuOpen: false,
                publicProfileOpen: false,
            };
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-stone-50 text-slate-800 antialiased dark:bg-gray-900">
@if(auth()->check() && request()->routeIs('dashboard', 'orders.*', 'cart.*', 'profile.*', 'settings.*', 'consumer.*', 'farmer.*', 'admin.*'))
    @include('layouts.toast')

    @php
        $user = auth()->user();
        if ($user->isAdmin()) {
            $navItems = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => 'dashboard', 'icon' => 'dashboard'],
                ['label' => 'User Reports', 'href' => route('admin.user-reports'), 'active' => 'admin.user-reports', 'icon' => 'users'],
                ['label' => 'Products', 'href' => route('admin.products'), 'active' => 'admin.products', 'icon' => 'products'],
                ['label' => 'Orders', 'href' => route('orders.index'), 'active' => 'orders.*', 'icon' => 'orders'],
                ['label' => 'Announcements', 'href' => route('admin.announcements.index'), 'active' => 'admin.announcements.*', 'icon' => 'megaphone'],
                ['label' => 'Activity Logs', 'href' => route('admin.activity-logs'), 'active' => 'admin.activity-logs', 'icon' => 'activity'],
            ];
        } elseif ($user->isFarmer()) {
            $navItems = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => 'dashboard', 'icon' => 'dashboard'],
                ['label' => 'My Products', 'href' => route('farmer.products.index'), 'active' => 'farmer.products.*', 'icon' => 'products'],
                ['label' => 'Inventory', 'href' => route('farmer.inventory.index'), 'active' => 'farmer.inventory.*', 'icon' => 'inventory'],
                ['label' => 'Orders', 'href' => route('orders.index'), 'active' => 'orders.*', 'icon' => 'orders'],
                ['label' => 'Decision Support', 'href' => route('farmer.decision-support'), 'active' => 'farmer.decision-support', 'icon' => 'chart'],
                ['label' => 'Sales Summary', 'href' => route('farmer.sales-summary'), 'active' => 'farmer.sales-summary', 'icon' => 'report'],
                ['label' => 'Settings', 'href' => route('settings.index'), 'active' => 'settings.*', 'icon' => 'activity'],
            ];
        } else {
            $navItems = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => 'dashboard', 'icon' => 'dashboard'],
                ['label' => 'Marketplace', 'href' => route('consumer.marketplace'), 'active' => 'consumer.marketplace', 'icon' => 'products'],
                ['label' => 'Cart', 'href' => route('cart.index'), 'active' => 'cart.*', 'icon' => 'cart'],
                ['label' => 'My Orders', 'href' => route('orders.index'), 'active' => ['orders.*', 'consumer.orders.*'], 'icon' => 'orders'],
                ['label' => 'Feedback', 'href' => route('consumer.feedback'), 'active' => 'consumer.feedback', 'icon' => 'star'],
            ];
        }

        $notificationItems = $user->notifications()
            ->latest()
            ->limit(8)
            ->get();
        $unreadNotificationCount = $user->unreadNotifications()->count();
    @endphp

    <div x-data="{ sidebarOpen: false, profileOpen: false, notificationsOpen: false }" class="min-h-screen">
        <aside class="fixed inset-y-0 left-0 z-40 w-[260px] -translate-x-full border-r border-slate-200 bg-white transition duration-200 md:translate-x-0 dark:border-gray-800 dark:bg-gray-950" :class="{ 'translate-x-0 shadow-2xl': sidebarOpen }">
            <div class="flex h-full flex-col">
                <div class="flex min-h-[72px] items-center border-b border-slate-100 px-4 dark:border-gray-800">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-700 text-white shadow-sm">
                            <x-ui.icon name="farmer" class="h-5 w-5" />
                        </span>
                        <span>
                            <span class="block text-lg font-black text-emerald-900 dark:text-emerald-400">SariwaLink</span>
                            <span class="block text-[11px] font-semibold uppercase tracking-wide text-slate-400">FARM-TO-MARKET PLATFORM</span>
                        </span>
                    </a>
                    <button type="button" class="absolute right-3 top-3 rounded-lg p-2 text-slate-400 hover:bg-slate-100 md:hidden" @click="sidebarOpen = false">
                        <x-ui.icon name="x" class="h-4 w-4" />
                    </button>
                </div>

                <nav class="flex-1 space-y-1.5 px-3 py-4">
                    @foreach($navItems as $item)
                        @php
                            $isActive = is_array($item['active'])
                                ? request()->routeIs(...$item['active'])
                                : request()->routeIs($item['active']);
                        @endphp
                            <a href="{{ $item['href'] }}" class="flex items-center rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $isActive ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-900 dark:text-gray-300 dark:hover:bg-gray-900' }}">
                                <span class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-white/15 text-white' : 'bg-slate-50 text-slate-500' }}">
                                    <x-ui.icon :name="$item['icon']" class="h-4 w-4" />
                                </span>
                                {{ $item['label'] }}
                            </a>
                    @endforeach
                </nav>

                <div class="border-t border-slate-100 p-3 dark:border-gray-800">
                    <div class="rounded-xl bg-stone-50 p-3 ring-1 ring-slate-100 dark:bg-gray-900">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-xs font-semibold text-emerald-700">{{ \Illuminate\Support\Str::title($user->role) }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="md:pl-[260px]">
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 px-4 py-0 backdrop-blur dark:border-gray-800 dark:bg-gray-950/95 md:px-6">
                <div class="flex min-h-[72px] items-center justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" class="rounded-xl border border-slate-200 bg-white p-2 text-slate-700 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 md:hidden" @click="sidebarOpen = true">
                            <x-ui.icon name="menu" class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="relative" @click.outside="notificationsOpen = false">
                            <button type="button" class="relative rounded-xl border border-slate-200 bg-white p-2 text-slate-600 shadow-sm hover:bg-stone-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800" @click="notificationsOpen = !notificationsOpen; profileOpen = false">
                                <x-ui.icon name="bell" class="h-5 w-5" />
                                @if($unreadNotificationCount > 0)
                                    <span class="absolute right-1.5 top-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-amber-500 px-1 text-[10px] font-black text-white">{{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}</span>
                                @endif
                            </button>
                            <div x-cloak x-show="notificationsOpen" x-transition class="absolute right-0 mt-2 w-80 rounded-2xl border border-slate-100 bg-white p-3 shadow-xl dark:border-gray-800 dark:bg-gray-900">
                                <div class="flex items-center justify-between gap-3 px-2 pb-2">
                                    <p class="text-sm font-black text-slate-900 dark:text-white">Notifications</p>
                                    @if($unreadNotificationCount > 0)
                                        <form method="POST" action="{{ route('notifications.read-all') }}">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 dark:text-emerald-300 dark:hover:text-emerald-200">Read all</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="space-y-2">
                                    @forelse($notificationItems as $notification)
                                        @php
                                            $notificationData = $notification->data ?? [];
                                            $notificationIcon = $notificationData['icon'] ?? 'bell';
                                            $notificationTitle = $notificationData['title'] ?? $notification->title;
                                            $notificationMessage = $notificationData['message'] ?? $notification->message;
                                        @endphp
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                            @csrf
                                            <button type="submit" class="flex w-full gap-3 rounded-xl p-3 text-left transition hover:bg-emerald-50 dark:hover:bg-gray-800 {{ $notification->isUnread() ? 'bg-emerald-50/80 ring-1 ring-emerald-100 dark:bg-emerald-950/30 dark:ring-emerald-900' : 'bg-stone-50 dark:bg-gray-800/70' }}">
                                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $notification->isUnread() ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                                    <x-ui.icon :name="$notificationIcon" class="h-4 w-4" />
                                                </span>
                                                <span class="min-w-0 flex-1">
                                                    <span class="flex items-start justify-between gap-2">
                                                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $notificationTitle }}</span>
                                                        @if($notification->isUnread())
                                                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-amber-500"></span>
                                                        @endif
                                                    </span>
                                                    <span class="block text-xs leading-relaxed text-slate-500 dark:text-gray-400">{{ $notificationMessage }}</span>
                                                    <span class="mt-1 block text-[11px] font-semibold text-slate-400 dark:text-gray-500">{{ $notification->created_at?->diffForHumans() }}</span>
                                                </span>
                                            </button>
                                        </form>
                                    @empty
                                        <p class="rounded-xl bg-stone-50 p-3 text-sm text-slate-500 dark:bg-gray-800 dark:text-gray-400">No new notifications.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="relative" @click.outside="profileOpen = false">
                            <button type="button" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2.5 py-2 shadow-sm hover:bg-stone-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800" @click="profileOpen = !profileOpen; notificationsOpen = false">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-xs font-black text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <span class="hidden text-left sm:block">
                                    <span class="block text-xs font-bold text-slate-900 dark:text-white">{{ \Illuminate\Support\Str::limit($user->name, 18) }}</span>
                                    <span class="block text-[11px] font-semibold text-emerald-700 dark:text-emerald-300">{{ \Illuminate\Support\Str::title($user->role) }}</span>
                                </span>
                            </button>
                            <div x-cloak x-show="profileOpen" x-transition class="absolute right-0 mt-2 w-64 rounded-2xl border border-slate-100 bg-white p-3 shadow-xl dark:border-gray-800 dark:bg-gray-900">
                                <div class="border-b border-slate-100 px-2 pb-3 dark:border-gray-800">
                                    <p class="font-black text-slate-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-sm text-slate-500 dark:text-gray-400">{{ $user->email }}</p>
                                    <span class="mt-2 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ \Illuminate\Support\Str::title($user->role) }}</span>
                                </div>
                                <div class="py-2">
                                    <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-stone-50 dark:text-gray-300 dark:hover:bg-gray-800">Profile</a>
                                    <a href="{{ route('settings.index') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-stone-50 dark:text-gray-300 dark:hover:bg-gray-800">Settings</a>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-2 dark:border-gray-800">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl px-3 py-2 text-left text-sm font-bold text-red-600 hover:bg-red-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="px-4 py-5 md:px-6">
                @yield('content')
            </main>
        </div>

        <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-30 bg-gray-900/30 md:hidden" @click="sidebarOpen = false"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($message = Session::get('success'))
                setTimeout(() => window.toast?.success('{{ $message }}'), 100);
            @endif
            @if($message = Session::get('error'))
                setTimeout(() => window.toast?.error('{{ $message }}'), 100);
            @endif
            @if($errors->any())
                setTimeout(() => @json($errors->all()).forEach(error => window.toast?.error(error)), 100);
            @endif
        });
    </script>
@else
    @php
        $publicNavItems = [
            ['label' => 'Home', 'href' => route('home'), 'active' => ['home'], 'icon' => 'home'],
            ['label' => 'Marketplace', 'href' => route('marketplace'), 'active' => ['marketplace', 'products.*'], 'icon' => 'products'],
            ['label' => 'About', 'href' => route('about'), 'active' => ['about'], 'icon' => 'info'],
        ];

        if (auth()->check()) {
            $publicNavItems[] = ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => ['dashboard'], 'icon' => 'dashboard'];
        }
    @endphp

    <div x-data="farmMartPublicShell()" class="min-h-screen bg-stone-50 text-slate-800 dark:bg-gray-950 dark:text-gray-100">
        <aside class="fixed inset-y-0 left-0 z-50 w-[240px] -translate-x-full border-r border-slate-200 bg-white transition duration-200 md:translate-x-0 dark:border-gray-800 dark:bg-gray-950" :class="{ 'translate-x-0 shadow-2xl': publicMenuOpen }">
            <div class="flex h-full flex-col">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="ml-4 mt-4 flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-700 text-white shadow-sm">
                        <x-ui.icon name="farmer" class="h-5 w-5" />
                    </span>
                    <span class="mt-4">
                        <span class="block text-xl font-black text-emerald-900 dark:text-emerald-400">SariwaLink</span>
                        <span class="hidden text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-gray-500 sm:block">FARM-TO-MARKET PLATFORM</span>
                    </span>
                </a>

                <nav class="mt-6 flex-1 space-y-1.5 px-3">
                    @foreach($publicNavItems as $item)
                        @php($isActive = request()->routeIs(...$item['active']))
                        <a href="{{ $item['href'] }}" class="flex items-center rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $isActive ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-900 dark:text-gray-300 dark:hover:bg-gray-900' }}" @click="publicMenuOpen = false">
                            <span class="mr-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-white/15 text-white' : 'bg-slate-50 text-slate-500 dark:bg-gray-900 dark:text-gray-400' }}">
                                <x-ui.icon :name="$item['icon']" class="h-4 w-4" />
                            </span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div class="md:pl-[240px]">
            <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-950/90">
                <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" class="rounded-xl border border-slate-200 bg-white p-2 text-slate-700 shadow-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 md:hidden" @click="publicMenuOpen = !publicMenuOpen" aria-label="Open navigation menu">
                            <x-ui.icon name="menu" class="h-5 w-5" />
                        </button>
                        <span class="truncate text-sm font-black text-emerald-900 dark:text-emerald-400 md:hidden">SariwaLink</span>
                    </div>

                    <div class="flex items-center gap-2 text-sm font-semibold">
                        @auth
                            <div class="relative" @click.outside="publicProfileOpen = false">
                                <button type="button" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2.5 py-2 shadow-sm dark:border-gray-800 dark:bg-gray-900" @click="publicProfileOpen = !publicProfileOpen">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-xs font-black text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                    <span class="hidden font-bold text-slate-700 dark:text-gray-200 sm:inline">{{ \Illuminate\Support\Str::limit(auth()->user()->name, 14) }}</span>
                                </button>
                                <div x-cloak x-show="publicProfileOpen" x-transition class="absolute right-0 mt-2 w-56 rounded-2xl border border-slate-100 bg-white p-3 shadow-xl dark:border-gray-800 dark:bg-gray-900">
                                    <a href="{{ route('dashboard') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-stone-50 dark:text-gray-300 dark:hover:bg-gray-800">Dashboard</a>
                                    <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-stone-50 dark:text-gray-300 dark:hover:bg-gray-800">Profile</a>
                                    <a href="{{ route('settings.index') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-stone-50 dark:text-gray-300 dark:hover:bg-gray-800">Settings</a>
                                    <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-slate-100 pt-2 dark:border-gray-800">
                                        @csrf
                                        <button type="submit" class="w-full rounded-xl px-3 py-2 text-left text-sm font-bold text-red-600 hover:bg-red-50">Logout</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-600 hover:text-emerald-800 dark:text-gray-300 dark:hover:text-emerald-300">Login</a>
                            <a href="{{ route('register') }}" class="rounded-xl bg-emerald-700 px-4 py-2 text-white hover:bg-emerald-800">Register</a>
                        @endauth
                    </div>
                </div>
            </header>

            @yield('content')
        </div>

        <div x-cloak x-show="publicMenuOpen" class="fixed inset-0 z-40 bg-gray-900/30 md:hidden" @click="publicMenuOpen = false"></div>
    </div>
@endif
</body>
</html>
