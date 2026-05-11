@extends('layouts.app')

@section('page-title', 'Change Password')

@section('content')
    <div class="max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Change Password</h2>
            </div>

            <form method="POST" action="{{ route('profile.update-password') }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Current Password</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('current_password') border-red-500 @enderror"
                    >
                    @error('current_password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">New Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('password') border-red-500 @enderror"
                    >
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Confirm Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg text-sm">
                    <p class="text-yellow-800 dark:text-yellow-200">Password must be at least 8 characters long.</p>
                </div>

                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold"
                    >
                        Change Password
                    </button>
                    <a href="{{ route('profile.edit') }}" class="bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white px-6 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition font-semibold">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
