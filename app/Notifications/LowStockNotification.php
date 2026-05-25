<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Product $product)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isOut = $this->product->quantity <= 0;

        return [
            'title' => $isOut ? 'Product Out of Stock' : 'Low Stock Alert',
            'message' => $isOut
                ? "{$this->product->name} is out of stock."
                : "{$this->product->name} is low in stock: {$this->product->quantity} {$this->product->unit} remaining.",
            'icon' => $isOut ? 'alert' : 'inventory',
            'product_id' => $this->product->id,
            'quantity' => $this->product->quantity,
            'unit' => $this->product->unit,
            'url' => route('farmer.inventory.index'),
        ];
    }
}
