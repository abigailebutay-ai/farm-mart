@extends('layouts.print')

@section('print-title', 'Sales Summary Report - Farm-Mart')
@section('print-heading', 'Sales Summary Report')
@section('print-generated', $dateGenerated->timezone(config('app.timezone'))->format('M d, Y h:i A'))
@section('print-meta')
    Farmer: {{ $farmer->name }}
@endsection

@section('content')
    <section class="print-summary">
        <div class="print-summary-card"><span class="print-summary-label">Total Sales</span><strong class="print-summary-value">PHP {{ number_format($totalSales, 2) }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Completed Orders</span><strong class="print-summary-value">{{ $completedOrderCount }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Sold Products</span><strong class="print-summary-value">{{ $soldProductsCount }}</strong></div>
        <div class="print-summary-card"><span class="print-summary-label">Total Revenue</span><strong class="print-summary-value">PHP {{ number_format($totalSales, 2) }}</strong></div>
    </section>

    <table>
        <thead>
            <tr>
                <th>Product name</th>
                <th>Quantity sold and unit</th>
                <th class="text-right">Revenue per product</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['quantity'] }} {{ $row['unit'] }}</td>
                    <td class="text-right">PHP {{ number_format($row['revenue'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="empty-row">No completed sales found.</td></tr>
            @endforelse
            <tr class="print-total-row">
                <td colspan="2">Total revenue</td>
                <td class="text-right">PHP {{ number_format($totalSales, 2) }}</td>
            </tr>
        </tbody>
    </table>
@endsection
