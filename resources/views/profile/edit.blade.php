@extends('layouts.app')

@section('page-title', 'Edit Profile')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Profile Settings</h2>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="flex items-center space-x-6">
                    <div>
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile" class="w-24 h-24 rounded-full object-cover">
                        @else
                            <div class="w-24 h-24 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                <span class="text-2xl">👤</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <label for="profile_picture" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Profile Picture</label>
                        <input
                            type="file"
                            id="profile_picture"
                            name="profile_picture"
                            accept="image/*"
                            class="block text-sm text-gray-500 dark:text-gray-400"
                        >
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PNG, JPG or GIF (max. 2MB)</p>
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Phone Number</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                    @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Address</label>
                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    >{{ old('address', $user->address) }}</textarea>
                    @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                    <p class="text-blue-800 dark:text-blue-200 text-sm">
                        <strong>Want to change your password?</strong>
                        <a href="{{ route('profile.password') }}" class="underline font-semibold">Click here</a>
                    </p>
                </div>

                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold"
                    >
                        Save Changes
                    </button>
                    <a href="{{ route('dashboard') }}" class="bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white px-6 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition font-semibold">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
