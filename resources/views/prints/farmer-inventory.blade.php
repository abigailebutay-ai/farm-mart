<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Report - Farm-Mart</title>
    @include('prints.partials.styles')
</head>
<body>
    <div class="report-actions no-print">
        <a href="{{ url()->previous() }}">Back</a>
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <main class="print-container">
        <div class="report-brand">Farm-Mart</div>
        <h1 class="print-title">Inventory Report</h1>
        <div class="print-meta">
            Farmer: {{ $farmer->name }}<br>
            Date generated: {{ $dateGenerated->format('M d, Y h:i A') }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product name</th>
                    <th>Category</th>
                    <th>Current stock quantity and unit</th>
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
                        <td>{{ $product->quantity }} {{ $product->unit ?? 'piece' }}</td>
                        <td>{{ $stockStatus }}</td>
                        <td>{{ $listingStatus }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">No inventory records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>
