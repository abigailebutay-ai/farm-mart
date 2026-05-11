@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="max-w-md mx-auto mt-12 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-500 p-6">
            <h1 class="text-3xl font-bold text-white">Login</h1>
            <p class="text-green-100 mt-2">Sign in to your Farmers Marketplace account</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="p-8">
            @csrf

            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('email') border-red-500 @enderror"
                    placeholder="your@email.com"
                    autofocus
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="••••••••"
                >
            </div>

            <div class="mb-6 flex items-center">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                >
                <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Remember me</label>
            </div>

            <button
                type="submit"
                class="w-full bg-green-600 text-white font-semibold py-2 rounded-lg hover:bg-green-700 transition mb-4"
            >
                Sign In
            </button>

            <p class="text-center text-gray-600 dark:text-gray-400">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-green-600 hover:text-green-700 font-semibold">Register here</a>
            </p>
        </form>
    </div>
@endsection
