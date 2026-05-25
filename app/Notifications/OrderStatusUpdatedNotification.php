<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class OrderStatusUpdatedNotification extends Notification
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
        $status = Str::headline($this->order->status);

        return [
            'title' => 'Order Status Updated',
            'message' => "Your order #{$this->order->id} is now {$status}.",
            'icon' => $this->order->status === 'cancelled' ? 'alert' : 'orders',
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'url' => route('orders.show', $this->order),
        ];
    }
}
