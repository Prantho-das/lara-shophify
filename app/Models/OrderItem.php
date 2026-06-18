<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($orderItem) {
            $order = $orderItem->order;
            if (!$order || !in_array($order->status, ['cancelled', 'returned'])) {
                $orderItem->adjustStock(-$orderItem->quantity);
            }
        });

        static::deleted(function ($orderItem) {
            $order = $orderItem->order;
            if (!$order || !in_array($order->status, ['cancelled', 'returned'])) {
                $orderItem->adjustStock($orderItem->quantity);
            }
        });

        static::updated(function ($orderItem) {
            $order = $orderItem->order;
            if (!$order || !in_array($order->status, ['cancelled', 'returned'])) {
                $qtyDiff = $orderItem->quantity - $orderItem->getOriginal('quantity');
                if ($qtyDiff !== 0) {
                    $orderItem->adjustStock(-$qtyDiff);
                }
            }
        });
    }

    public function adjustStock($qty)
    {
        if ($this->variant_id) {
            $variant = ProductVariant::find($this->variant_id);
            if ($variant) {
                $variant->increment('stock_quantity', $qty);
            }
        } else {
            $product = Product::find($this->product_id);
            if ($product) {
                $product->increment('stock_quantity', $qty);
            }
        }
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
