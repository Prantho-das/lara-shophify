<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResellerProfile extends Model
{
    protected $guarded = [];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'is_active' => 'boolean',
        'custom_price_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prices()
    {
        return $this->hasMany(ResellerPrice::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getResellerPriceFor(Product $product, ?ProductVariant $variant = null): ?float
    {
        if (!$this->custom_price_enabled) {
            return null;
        }

        $price = $this->prices()
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        return $price?->custom_price;
    }

    public function getDiscountedPrice(float $basePrice): float
    {
        return max(0, $basePrice * (1 - ($this->discount_percent / 100)));
    }
}
