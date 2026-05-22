@extends('layouts.app')

@section('title', 'About')

@section('content')
    <main class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8">
        <x-ui.page-header
            title="About Farm-Mart"
            subtitle="Farm-Mart is a thesis system for farmer-to-buyer transactions, inventory monitoring, order fulfillment, sales summaries, and decision support."
        />

        <div class="mb-6 h-px bg-gradient-to-r from-transparent via-emerald-200 to-transparent dark:via-emerald-800/70"></div>

        <div class="grid gap-5 md:grid-cols-3">
            <x-ui.dashboard-card title="Digital Supply Chain">
                <p class="text-sm text-gray-500 dark:text-gray-400">Connects local farmers and buyers through a structured online marketplace.</p>
            </x-ui.dashboard-card>
            <x-ui.dashboard-card title="Decision Support">
                <p class="text-sm text-gray-500 dark:text-gray-400">Highlights low stock, best-selling products, high demand, and monthly sales information.</p>
            </x-ui.dashboard-card>
            <x-ui.dashboard-card title="Role-Based Dashboards">
                <p class="text-sm text-gray-500 dark:text-gray-400">Provides clean dashboards and role-based workflows for farmers, buyers, and administrators.</p>
            </x-ui.dashboard-card>
        </div>
    </main>
@endsection
