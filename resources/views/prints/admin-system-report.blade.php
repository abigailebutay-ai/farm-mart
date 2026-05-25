@extends('layouts.print')

@section('print-title', 'Admin System Report - Farm-Mart')
@section('print-heading', 'Admin System Report')
@section('print-report-date', $reportDate->timezone(config('app.timezone'))->format('M d, Y h:i A'))

@section('content')
    <section class="print-summary">
        <div class="print-summary-card"><span class="print-summary-label">Total Users</span><strong class="print-summary-value">{{ $totalUsers }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Total Farmers</span><strong class="print-summary-value">{{ $totalFarmers }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Total Buyers/Consumers</span><strong class="print-summary-value">{{ $totalBuyers }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Total Products</span><strong class="print-summary-value">{{ $totalProducts }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Pending Verifications</span><strong class="print-summary-value">{{ $pendingVerifications }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Total Orders</span><strong class="print-summary-value">{{ $totalOrders }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Completed Orders</span><strong class="print-summary-value">{{ $completedOrders }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Cancelled Orders</span><strong class="print-summary-value">{{ $cancelledOrders }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Total Revenue</span><strong class="print-summary-value">PHP {{ number_format($totalRevenue, 2) }}</strong></div>
    </section>
@endsection
