<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'period', 'demand_score', 'trend_direction'])]
class DemandTrend extends Model
{
    use HasFactory;

    protected $table = 'demand_trends';

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'demand_score' => 'decimal:2',
        ];
    }

    /**
     * Get the product for this demand trend.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
