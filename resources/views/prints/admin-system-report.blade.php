<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin System Report - Farm-Mart</title>
    @include('prints.partials.styles')
</head>
<body>
    <div class="report-actions no-print">
        <a href="{{ url()->previous() }}">Back</a>
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <main class="print-container">
        <div class="report-brand">Farm-Mart</div>
        <h1 class="print-title">Admin System Report</h1>
        <div class="print-meta">
            Date generated: {{ $dateGenerated->format('M d, Y h:i A') }}
        </div>

        <section class="summary-grid">
            <div class="summary-card"><span>Total users</span><strong>{{ $totalUsers }}</strong></div>
            <div class="summary-card"><span>Total farmers</span><strong>{{ $totalFarmers }}</strong></div>
            <div class="summary-card"><span>Total buyers/consumers</span><strong>{{ $totalBuyers }}</strong></div>
            <div class="summary-card"><span>Total products</span><strong>{{ $totalProducts }}</strong></div>
            <div class="summary-card"><span>Pending verifications</span><strong>{{ $pendingVerifications }}</strong></div>
            <div class="summary-card"><span>Total orders</span><strong>{{ $totalOrders }}</strong></div>
            <div class="summary-card"><span>Completed orders</span><strong>{{ $completedOrders }}</strong></div>
            <div class="summary-card"><span>Cancelled orders</span><strong>{{ $cancelledOrders }}</strong></div>
        </section>

        <table>
            <tbody>
                <tr>
                    <th>Total revenue</th>
                    <td class="text-right">PHP {{ number_format($totalRevenue, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>
