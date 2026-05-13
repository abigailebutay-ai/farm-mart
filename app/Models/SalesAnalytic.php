<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'date', 'quantity_sold', 'total_revenue', 'avg_price'])]
class SalesAnalytic extends Model
{
    use HasFactory;

    protected $table = 'sales_analytics';

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_revenue' => 'decimal:2',
            'avg_price' => 'decimal:2',
        ];
    }

    /**
     * Get the product for this sales analytic record.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
