<!DOCTYPE html>
<html lang="en" @if(auth()->user() && auth()->user()->dark_mode) dark @endif>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Farmers Marketplace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    @auth
        <!-- Toast Container -->
        @include('layouts.toast')

        <div class="flex h-screen bg-gray-100 dark:bg-gray-800">
            <!-- Sidebar -->
            <div class="hidden md:flex md:w-64 md:flex-col">
                <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto bg-white dark:bg-gray-950 border-r border-gray-200 dark:border-gray-700">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <h1 class="text-2xl font-bold text-green-600 dark:text-green-400">🌾 FarmMart</h1>
                    </div>
                    <nav class="mt-5 flex-1 flex flex-col px-2 space-y-1">
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition @if(request()->routeIs('dashboard')) bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-400 @endif">
                                <span class="text-lg mr-3 w-5">📊</span>
                                <span>Dashboard</span>
                            </a>

                            @if(auth()->user()->isConsumer())
                                <a href="{{ route('products.index') }}" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition @if(request()->routeIs('products.index')) bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-400 @endif">
                                    <span class="text-lg mr-3 w-5">🛒</span>
                                    <span>Browse</span>
                                </a>
                                <a href="{{ route('cart.index') }}" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition @if(request()->routeIs('cart.index')) bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-400 @endif">
                                    <span class="text-lg mr-3 w-5">🛒</span>
                                    <span>My Cart</span>
                                </a>
                            @endif

                            @if(auth()->user()->isFarmer())
                                <a href="{{ route('farmer.products.index') }}" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition @if(request()->routeIs('farmer.products.index')) bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-400 @endif">
                                    <span class="text-lg mr-3 w-5">📦</span>
                                    <span>My Products</span>
                                </a>
                                <a href="{{ route('farmer.products.create') }}" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                    <span class="text-lg mr-3 w-5">➕</span>
                                    <span>Add Product</span>
                                </a>
                            @endif

                            <a href="{{ route('orders.index') }}" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition @if(request()->routeIs('orders.index')) bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-400 @endif">
                                <span class="text-lg mr-3 w-5">📋</span>
                                <span>Orders</span>
                            </a>
                        </div>

                        <hr class="my-3 border-gray-200 dark:border-gray-700">

                        <div class="space-y-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition @if(request()->routeIs('profile.edit')) bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-400 @endif">
                                <span class="text-lg mr-3 w-5">👤</span>
                                <span>Profile</span>
                            </a>
                            <a href="{{ route('settings.index') }}" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition @if(request()->routeIs('settings.index')) bg-green-50 dark:bg-green-900 text-green-600 dark:text-green-400 @endif">
                                <span class="text-lg mr-3 w-5">⚙️</span>
                                <span>Settings</span>
                            </a>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                            @csrf
                            <button type="submit" class="flex items-center w-full text-left px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                <span class="text-lg mr-3 w-5">🚪</span>
                                <span>Logout</span>
                            </button>
                        </form>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Navigation -->
                <div class="bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-700 px-4 py-4 md:px-8 md:py-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl md:text-3xl font-bold text-gray-900 dark:text-white">@yield('page-title', 'Dashboard')</h2>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ auth()->user()->name }}</span>
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">
                                {{ Str::ucfirst(auth()->user()->role) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-auto relative z-0">
                    <main class="p-4 md:p-8">
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>

        <!-- Flash Messages to Toasts Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if($message = Session::get('success'))
                    setTimeout(() => {
                        if (window.toast) {
                            window.toast.success('{{ $message }}');
                        }
                    }, 100);
                @endif

                @if($message = Session::get('error'))
                    setTimeout(() => {
                        if (window.toast) {
                            window.toast.error('{{ $message }}');
                        }
                    }, 100);
                @endif

                @if($errors->any())
                    setTimeout(() => {
                        if (window.toast) {
                            const errors = @json($errors->all());
                            errors.forEach(error => {
                                window.toast.error(error);
                            });
                        }
                    }, 100);
                @endif
            });
        </script>

        <!-- Mobile Menu Button (for future implementation) -->
        <div class="md:hidden fixed bottom-4 right-4 z-50">
            <!-- Mobile menu can be added here -->
        </div>
    @else
        <!-- Guest Layout -->
        <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 dark:from-gray-900 dark:to-gray-800">
            <!-- Top Navigation -->
            <nav class="bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-700">
                <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-green-600 dark:text-green-400">🌾 FarmMart</h1>
                    <div class="space-x-4">
                        @if(Route::has('login'))
                            <a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">Login</a>
                            <a href="{{ route('register') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Register</a>
                        @endif
                    </div>
                </div>
            </nav>

            <div class="container mx-auto px-4 py-12">
                @yield('content')
            </div>
        </div>
    @endauth
</body>
</html>
