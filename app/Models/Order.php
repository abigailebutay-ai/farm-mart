<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subtotal',
        'total',
        'status',
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
