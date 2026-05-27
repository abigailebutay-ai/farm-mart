@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-xl shadow-emerald-900/5">
            <div class="bg-gradient-to-r from-emerald-950 to-lime-800 p-6 text-white md:p-8">
                <p class="text-sm font-black uppercase tracking-wide text-amber-300">Create Account</p>
                <h1 class="mt-2 text-3xl font-black">Join SariwaLink</h1>
                <p class="mt-2 text-emerald-100">Register as a farmer seller or buyer to access the digital farm-to-market platform.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="grid gap-5 p-6 md:grid-cols-2 md:p-8">
                @csrf

                <div class="md:col-span-2">
                    <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 @error('name') border-red-500 @enderror" placeholder="Juan Dela Cruz" autofocus>
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 @error('email') border-red-500 @enderror" placeholder="your@email.com">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="role" class="mb-2 block text-sm font-bold text-slate-700">Account Type</label>
                    <select id="role" name="role" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        <option value="">Select your role</option>
                        <option value="consumer" @selected(old('role') === 'consumer')>Consumer (Buyer)</option>
                        <option value="farmer" @selected(old('role') === 'farmer')>Farmer (Seller)</option>
                    </select>
                    @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-sm font-bold text-slate-700">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100" placeholder="09XX-XXX-XXXX">
                    @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="address" class="mb-2 block text-sm font-bold text-slate-700">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100" placeholder="City, Province">
                    @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-slate-700">Password</label>
                    <input type="password" id="password" name="password" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 @error('password') border-red-500 @enderror" placeholder="Create a password">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100" placeholder="Confirm your password">
                </div>

                <div class="md:col-span-2">
                    <x-ui.primary-button class="w-full py-3">Create Account</x-ui.primary-button>
                    <p class="mt-4 text-center text-sm text-slate-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-bold text-emerald-800 hover:text-emerald-900">Login here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection
