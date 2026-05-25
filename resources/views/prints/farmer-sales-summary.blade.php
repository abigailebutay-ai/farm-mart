<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales Summary Report - Farm-Mart</title>
    @include('prints.partials.styles')
</head>
<body>
    <div class="report-actions no-print">
        <a href="{{ url()->previous() }}">Back</a>
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <main class="print-container">
        <div class="report-brand">Farm-Mart</div>
        <h1 class="print-title">Sales Summary Report</h1>
        <div class="print-meta">
            Farmer: {{ $farmer->name }}<br>
            Date generated: {{ $dateGenerated->format('M d, Y h:i A') }}
        </div>

        <section class="summary-grid">
            <div class="summary-card">
                <span>Total sales</span>
                <strong>PHP {{ number_format($totalSales, 2) }}</strong>
            </div>
            <div class="summary-card">
                <span>Completed orders</span>
                <strong>{{ $completedOrderCount }}</strong>
            </div>
            <div class="summary-card">
                <span>Sold products</span>
                <strong>{{ $soldProductsCount }}</strong>
            </div>
            <div class="summary-card">
                <span>Total revenue</span>
                <strong>PHP {{ number_format($totalSales, 2) }}</strong>
            </div>
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
                    <tr>
                        <td colspan="3" class="empty-row">No completed sales found.</td>
                    </tr>
                @endforelse
                <tr>
                    <th colspan="2">Total revenue</th>
                    <th class="text-right">PHP {{ number_format($totalSales, 2) }}</th>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>
