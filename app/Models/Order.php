<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subtotal',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_proof',
        'notes',
    ];

    /**
     * Get the consumer who placed this order.
     */
    public function consumer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
            return 'Order is already cancelled';
        }

        if ($this->status === 'preparing') {
            return 'Order is already being prepared';
        }

        if ($this->status === 'completed') {
            return 'Completed orders cannot be cancelled';
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
            'pending_verification' => 'Pending Verification',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            default => 'Pending',
        };
    }

    public function paymentProofUrl(): ?string
    {
        if (! $this->payment_proof) {
            return null;
        }

        return Storage::disk(config('filesystems.default'))->url($this->payment_proof);
    }

    public function paymentProofIsImage(): bool
    {
        if (! $this->payment_proof) {
            return false;
        }

        return in_array(Str::lower(pathinfo($this->payment_proof, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
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
