<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'subtotal', 'total'])]
class Cart extends Model
{
    use HasFactory;

    /**
     * Get the consumer who owns this cart.
     */
    public function consumer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all cart items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate and update the cart totals.
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items()->sum('subtotal');
        $this->total = $this->subtotal;
        $this->save();
    }
}
