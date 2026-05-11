<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['cart_id', 'product_id', 'quantity', 'price', 'subtotal'])]
class CartItem extends Model
{
    use HasFactory;

    /**
     * Get the cart this item belongs to.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Update the subtotal based on quantity and price.
     */
    public function updateSubtotal(): void
    {
        $this->subtotal = $this->quantity * $this->price;
        $this->save();
        $this->cart->calculateTotals();
    }
}
