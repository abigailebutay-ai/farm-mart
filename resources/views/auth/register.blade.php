@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="max-w-lg mx-auto mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-500 p-6">
            <h1 class="text-3xl font-bold text-white">Create Account</h1>
            <p class="text-green-100 mt-2">Join Farmers Marketplace today</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="p-8 space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('name') border-red-500 @enderror"
                    placeholder="John Doe"
                    autofocus
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('email') border-red-500 @enderror"
                    placeholder="your@email.com"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">I am a:</label>
                <select
                    id="role"
                    name="role"
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                >
                    <option value="">Select your role</option>
                    <option value="consumer" @selected(old('role') === 'consumer')>Consumer (Buyer)</option>
                    <option value="farmer" @selected(old('role') === 'farmer')>Farmer (Seller)</option>
                </select>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Phone Number (Optional)</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="09XX-XXX-XXXX"
                >
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Address (Optional)</label>
                <textarea
                    id="address"
                    name="address"
                    rows="2"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="123 Main St, City, Province"
                >{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('password') border-red-500 @enderror"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Confirm Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    placeholder="••••••••"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-green-600 text-white font-semibold py-2 rounded-lg hover:bg-green-700 transition mt-6"
            >
                Create Account
            </button>

            <p class="text-center text-gray-600 dark:text-gray-400 mt-4">
                Already have an account?
                <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-semibold">Login here</a>
            </p>
        </form>
    </div>
@endsection
