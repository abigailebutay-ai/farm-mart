@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="mx-auto grid min-h-[calc(100vh-80px)] max-w-6xl items-center gap-8 px-4 py-12 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
        <div class="hidden rounded-[2rem] bg-gradient-to-br from-emerald-950 to-lime-800 p-8 text-white shadow-xl lg:block">
            <p class="text-sm font-black uppercase tracking-wide text-amber-300">Farm-Mart Access</p>
            <h1 class="mt-4 text-4xl font-black">Fresh produce marketplace and decision-support dashboard.</h1>
            <p class="mt-4 leading-relaxed text-emerald-100">Sign in to manage products, monitor orders, browse fresh local produce, or review supply chain activity.</p>
        </div>

        <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-xl shadow-emerald-900/5 md:p-8">
            <h1 class="text-3xl font-black text-slate-900">Login</h1>
            <p class="mt-2 text-slate-500">Sign in to your Farm-Mart account.</p>

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 @error('email') border-red-500 @enderror" placeholder="your@email.com" autofocus>
                    @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-slate-700">Password</label>
                    <input type="password" id="password" name="password" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100" placeholder="Enter your password">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-500">
                    <label for="remember" class="ml-2 block text-sm text-slate-600">Remember me</label>
                </div>

                <x-ui.primary-button class="w-full py-3">Sign In</x-ui.primary-button>

                <p class="text-center text-sm text-slate-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-bold text-emerald-800 hover:text-emerald-900">Register here</a>
                </p>
            </form>
        </div>
    </div>
@endsection
