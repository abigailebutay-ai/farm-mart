<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'product_id', 'alert_type', 'message', 'threshold', 'actual_value'])]
class InventoryAlert extends Model
{
    use HasFactory;

    /**
     * Get the farmer who owns this alert.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product this alert is for.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
