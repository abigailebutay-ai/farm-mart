@extends('layouts.print')

@section('print-title', 'Products Report - SariwaLink')
@section('print-heading', 'Products Report')
@section('print-report-date', $reportDate->timezone(config('app.timezone'))->format('M d, Y h:i A'))

@section('content')
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Farmer</th>
                <th>Category</th>
                <th>Price per kg</th>
                <th>Stock</th>
                <th>Stock status</th>
                <th>Created date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                @php
                    $stockStatus = $product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock');
                @endphp
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->farmer->name ?? 'Unknown farmer' }}</td>
                    <td>{{ $product->category }}</td>
                    <td>PHP {{ number_format($product->price, 2) }} / kg</td>
                    <td>{{ $product->quantity }} kg</td>
                    <td>{{ $stockStatus }}</td>
                    <td>{{ optional($product->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-row">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
