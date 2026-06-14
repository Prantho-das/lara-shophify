<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = [];

    protected $casts = [
        'attribute_values' => 'array',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSellingPriceAttribute()
    {
        $price = (float) $this->price;
        $parent = $this->product;
        if ($parent && $parent->category && $parent->category->discount_value > 0) {
            if ($parent->category->discount_type === 'percent') {
                return max(0, $price * (1 - ($parent->category->discount_value / 100)));
            } elseif ($parent->category->discount_type === 'fixed') {
                return max(0, $price - $parent->category->discount_value);
            }
        }
        return $price;
    }

    public function getComparePriceDisplayAttribute()
    {
        $parent = $this->product;
        if ($parent && $parent->category && $parent->category->discount_value > 0) {
            return $this->compare_price ?: $this->price;
        }
        return $this->compare_price;
    }
}
