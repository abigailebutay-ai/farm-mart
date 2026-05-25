@extends('layouts.print')

@section('print-title', 'Orders Report - Farm-Mart')
@section('print-heading', 'Orders Report')
@section('print-generated', $dateGenerated->timezone(config('app.timezone'))->format('M d, Y h:i A'))

@section('content')
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
                    $farmers = $order->items->pluck('farmer.name')->filter()->unique()->values();
                    $createdDate = optional($order->created_at)->timezone(config('app.timezone'))->format('M d, Y h:i A');
                    $completedDate = $order->status === 'completed'
                        ? optional($order->updated_at)->timezone(config('app.timezone'))->format('M d, Y h:i A')
                        : null;
                @endphp
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->consumer->name ?? 'Buyer unavailable' }}</td>
                    <td>{{ $farmers->isNotEmpty() ? $farmers->join(', ') : 'Not available' }}</td>
                    <td>{{ $order->items_count ?? $order->items->count() }}</td>
                    <td>PHP {{ number_format($order->total, 2) }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>
                        Date Created: {{ $createdDate }}
                        @if($completedDate)
                            <br>Completed At: {{ $completedDate }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-row">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
