@extends('layouts.print')

@section('print-title', 'Inventory Report - SariwaLink')
@section('print-heading', 'Inventory Report')
@section('print-report-date', $reportDate->timezone(config('app.timezone'))->format('M d, Y h:i A'))
@section('print-meta')
    Farmer: {{ $farmer->name }}
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>Product name</th>
                <th>Category</th>
                <th>Current Stock (kg)</th>
                <th>Stock status</th>
                <th>Listing status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                @php
                    $stockStatus = $product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock');
                    $listingStatus = $product->status ?? ($product->quantity > 0 ? 'Active' : 'Out of Stock');
                @endphp
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category }}</td>
                    <td>{{ $product->quantity }} kg</td>
                    <td>{{ $stockStatus }}</td>
                    <td>{{ $listingStatus }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-row">No inventory records found.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
