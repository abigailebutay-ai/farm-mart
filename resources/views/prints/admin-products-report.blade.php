@extends('layouts.print')

@section('print-title', 'Products Report - Farm-Mart')
@section('print-heading', 'Products Report')
@section('print-generated', $dateGenerated->timezone(config('app.timezone'))->format('M d, Y h:i A'))

@section('content')
    <table>
        <thead>
            <tr>
                <th>Product name</th>
                <th>Farmer/seller</th>
                <th>Category</th>
                <th>Price per unit</th>
                <th>Stock quantity and unit</th>
                <th>Stock status</th>
                <th>Listing status</th>
                <th>Created date</th>
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
                    <td>{{ $product->farmer->name ?? 'Unknown farmer' }}</td>
                    <td>{{ $product->category }}</td>
                    <td>PHP {{ number_format($product->price, 2) }} / {{ $product->unit ?? 'piece' }}</td>
                    <td>{{ $product->quantity }} {{ $product->unit ?? 'piece' }}</td>
                    <td>{{ $stockStatus }}</td>
                    <td>{{ $listingStatus }}</td>
                    <td>{{ optional($product->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty-row">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
