<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'attributes' => 'array',
        'tags' => 'array',
        'has_variants' => 'boolean',
        'featured' => 'boolean',
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->stocks()->sum('quantity');
    }

    public function getSellingPriceAttribute()
    {
        $basePrice = (float) $this->base_price;
        if ($this->category && $this->category->discount_value > 0) {
            if ($this->category->discount_type === 'percent') {
                return max(0, $basePrice * (1 - ($this->category->discount_value / 100)));
            } elseif ($this->category->discount_type === 'fixed') {
                return max(0, $basePrice - $this->category->discount_value);
            }
        }
        return $basePrice;
    }

    public function getComparePriceDisplayAttribute()
    {
        if ($this->category && $this->category->discount_value > 0) {
            return $this->compare_price ?: $this->base_price;
        }
        return $this->compare_price;
    }

    public function getResellerPrice(?ResellerProfile $reseller = null): float
    {
        if (!$reseller) {
            return $this->selling_price;
        }

        $customPrice = $reseller->getResellerPriceFor($this);
        if ($customPrice !== null) {
            return $customPrice;
        }

        return $reseller->getDiscountedPrice($this->base_price);
    }
}
