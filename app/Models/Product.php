<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'price',
        'quantity',
        'unit',
        'status',
        'image',
    ];

    public const UNITS = [
        'kg',
        'gram',
        'piece',
        'bundle',
        'sack',
        'tray',
        'box',
        'bunch',
        'liter',
        'pack',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        $path = $this->image_storage_path;

        return $path
            ? Storage::disk(config('filesystems.default'))->url($path)
            : null;
    }

    public function getImageStoragePathAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        $path = str_replace('\\', '/', $this->image);
        $path = parse_url($path, PHP_URL_PATH) ?: $path;

        foreach (['/storage/', 'storage/', 'storage/app/public/', 'public/'] as $prefix) {
            if (Str::contains($path, $prefix)) {
                $path = Str::after($path, $prefix);
            }
        }

        if (Str::contains($path, 'products/')) {
            return 'products/' . Str::after($path, 'products/');
        }

        return ltrim($path, '/');
    }

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
