<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders Report - Farm-Mart</title>
    @include('prints.partials.styles')
</head>
<body>
    <div class="report-actions no-print">
        <a href="{{ url()->previous() }}">Back</a>
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <main class="print-container">
        <div class="report-brand">Farm-Mart</div>
        <h1 class="print-title">Orders Report</h1>
        <div class="print-meta">
            Date generated: {{ $dateGenerated->format('M d, Y h:i A') }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer name</th>
                    <th>Farmer/seller</th>
                    <th>Items count</th>
                    <th>Total amount</th>
                    <th>Status</th>
                    <th>Date created/completed</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $farmers = $order->items
                            ->pluck('farmer.name')
                            ->filter()
                            ->unique()
                            ->values();
                        $completedDate = $order->status === 'completed' ? optional($order->updated_at)->format('M d, Y h:i A') : null;
                    @endphp
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->consumer->name ?? 'Buyer unavailable' }}</td>
                        <td>{{ $farmers->isNotEmpty() ? $farmers->join(', ') : 'Not available' }}</td>
                        <td>{{ $order->items_count ?? $order->items->count() }}</td>
                        <td>PHP {{ number_format($order->total, 2) }}</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>
                            Created: {{ optional($order->created_at)->format('M d, Y h:i A') }}
                            @if($completedDate)
                                <br>Completed: {{ $completedDate }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>
