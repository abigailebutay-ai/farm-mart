<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'coupon_code',
        'discount_label',
        'discount_type',
        'discount_rate',
        'discount_amount',
        'total_kg',
        'subtotal',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'refund_status',
        'refund_reference',
        'refund_note',
        'refunded_at',
        'payment_reference',
        'payment_proof',
        'gcash_payee_name',
        'gcash_payee_number',
        'fulfillment_method',
        'completion_proof',
        'completion_note',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    /**
     * Get the consumer who placed this order.
     */
    public function consumer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get all items in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function canBeCancelledByConsumer(): bool
    {
        return $this->user_id === auth()->id()
            && in_array($this->status, ['pending', 'accepted'], true)
            && $this->created_at?->greaterThanOrEqualTo(now()->subDay());
    }

    public function consumerCancellationMessage(): string
    {
        if ($this->status === 'cancelled') {
            return 'Order cancelled';
        }

        if ($this->status === 'preparing') {
            return 'Order is already being prepared';
        }

        if ($this->status === 'out_for_delivery') {
            return 'Order is on the way';
        }

        if ($this->status === 'ready_for_pickup') {
            return 'Waiting for buyer pickup';
        }

        if ($this->status === 'completed') {
            return 'Order completed';
        }

        if ($this->created_at?->lt(now()->subDay())) {
            return 'Cancellation period ended';
        }

        return 'This order can no longer be cancelled';
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'gcash' => 'GCash',
            'cod', null => 'Cash on Delivery',
            default => Str::title(str_replace('_', ' ', $this->payment_method)),
        };
    }

    public function paymentStatusLabel(): string
    {
        if (in_array($this->payment_method, ['cod', null], true)) {
            return $this->status === 'completed' ? 'Paid' : 'Pending';
        }

        return match ($this->payment_status) {
            'pending_farmer_confirmation', 'pending_verification' => 'Pending Farmer Confirmation',
            'refund_pending' => 'Refund Pending',
            'refunded' => 'Refunded',
            'paid' => 'Paid',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            default => 'Pending',
        };
    }

    public function refundStatusLabel(): string
    {
        return match ($this->refund_status) {
            'pending' => 'Refund Pending',
            'refunded' => 'Refunded',
            default => 'No Refund',
        };
    }

    public function paymentProofUrl(): ?string
    {
        if (! $this->payment_proof) {
            return null;
        }

        $path = str_replace('\\', '/', $this->payment_proof);
        $path = ltrim($path, '/');

        return route('payment.proof', ['path' => $path]);
    }

    public function paymentProofIsImage(): bool
    {
        if (! $this->payment_proof) {
            return false;
        }

        return in_array(Str::lower(pathinfo($this->payment_proof, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }

    public function fulfillmentMethod(): string
    {
        return $this->fulfillment_method === 'pickup' ? 'pickup' : 'delivery';
    }

    public function fulfillmentMethodLabel(): string
    {
        return $this->fulfillmentMethod() === 'pickup' ? 'Pick up' : 'Delivery';
    }

    public function completionProofLabel(): string
    {
        return $this->fulfillmentMethod() === 'pickup' ? 'Proof of Pickup' : 'Proof of Delivery';
    }

    public function completionProofUrl(): ?string
    {
        if (! $this->completion_proof) {
            return null;
        }

        $path = str_replace('\\', '/', $this->completion_proof);
        $path = ltrim($path, '/');

        return route('completion.proof', ['path' => $path]);
    }

    public function completionProofIsImage(): bool
    {
        if (! $this->completion_proof) {
            return false;
        }

        return in_array(Str::lower(pathinfo($this->completion_proof, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }

    /**
     * Mark order as accepted.
     */
    public function accept(): void
    {
        $this->status = 'accepted';
        $this->save();
    }

    /**
     * Mark order as completed.
     */
    public function complete(): void
    {
        $this->status = 'completed';

        if (in_array($this->payment_method, ['cod', null], true)) {
            $this->payment_status = 'paid';
        }

        $this->completed_at = now();
        $this->save();
    }

    public function markOutForDelivery(): void
    {
        $this->status = 'out_for_delivery';
        $this->save();
    }

    public function markReadyForPickup(): void
    {
        $this->status = 'ready_for_pickup';
        $this->save();
    }

    /**
     * Mark order as cancelled.
     */
    public function cancel(): void
    {
        $this->status = 'cancelled';
        $this->save();
    }
}
