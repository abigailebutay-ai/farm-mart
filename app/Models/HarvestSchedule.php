<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HarvestSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'planned_date',
        'estimated_yield',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'planned_date' => 'date',
            'estimated_yield' => 'decimal:2',
        ];
    }

    /**
     * Get the farmer who owns this harvest schedule.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product for this harvest schedule.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
