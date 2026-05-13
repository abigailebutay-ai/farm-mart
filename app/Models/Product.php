<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'description', 'category', 'price', 'quantity', 'image'])]
class Product extends Model
{
    use HasFactory;

    /**
     * Get the farmer who owns this product.
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all cart items for this product.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get all order items for this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get all ratings for this product.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get all reviews for this product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all wishlist entries for this product.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get all images for this product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get all inventory alerts for this product.
     */
    public function inventoryAlerts(): HasMany
    {
        return $this->hasMany(InventoryAlert::class);
    }

    /**
     * Get all harvest schedules for this product.
     */
    public function harvestSchedules(): HasMany
    {
        return $this->hasMany(HarvestSchedule::class);
    }

    /**
     * Get all sales analytics for this product.
     */
    public function salesAnalytics(): HasMany
    {
        return $this->hasMany(SalesAnalytic::class);
    }

    /**
     * Get all demand trends for this product.
     */
    public function demandTrends(): HasMany
    {
        return $this->hasMany(DemandTrend::class);
    }
}
