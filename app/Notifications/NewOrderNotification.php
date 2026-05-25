<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Order Received',
            'message' => 'New order received from ' . ($this->order->consumer->name ?? 'a buyer') . '.',
            'icon' => 'orders',
            'order_id' => $this->order->id,
            'buyer_name' => $this->order->consumer->name ?? null,
            'total' => $this->order->total,
            'url' => route('orders.show', $this->order),
        ];
    }
}
