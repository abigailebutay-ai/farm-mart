@extends('layouts.app')

@section('page-title', 'Settings')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Account Settings</h2>
            </div>

            <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Display Preferences</h3>
                    
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label for="dark_mode" class="block text-sm font-semibold text-gray-900 dark:text-white mb-1">Dark Mode</label>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Enable dark mode for a comfortable viewing experience</p>
                        </div>
                        <div>
                            <input
                                type="checkbox"
                                id="dark_mode"
                                name="dark_mode"
                                value="1"
                                @checked($user->dark_mode)
                                class="h-6 w-6 text-green-600 focus:ring-green-500 border-gray-300 rounded cursor-pointer"
                            >
                        </div>
                    </div>
                </div>

                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notification Settings</h3>
                    
                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label for="notification_enabled" class="block text-sm font-semibold text-gray-900 dark:text-white mb-1">Enable Notifications</label>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                @if($user->isFarmer())
                                    Receive notifications about new orders and order updates
                                @else
                                    Receive notifications about order updates and product recommendations
                                @endif
                            </p>
                        </div>
                        <div>
                            <input
                                type="checkbox"
                                id="notification_enabled"
                                name="notification_enabled"
                                value="1"
                                @checked($user->notification_enabled)
                                class="h-6 w-6 text-green-600 focus:ring-green-500 border-gray-300 rounded cursor-pointer"
                            >
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                    <p class="text-blue-800 dark:text-blue-200 text-sm">
                        <strong>Account Type:</strong> {{ Str::ucfirst($user->role) }}
                    </p>
                </div>

                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold"
                    >
                        Save Settings
                    </button>
                    <a href="{{ route('dashboard') }}" class="bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white px-6 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition font-semibold">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
