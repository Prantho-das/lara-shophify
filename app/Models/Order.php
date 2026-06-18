<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted()
    {
        static::updated(function ($order) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            if ($oldStatus !== $newStatus) {
                $isOldInactive = in_array($oldStatus, ['cancelled', 'returned']);
                $isNewInactive = in_array($newStatus, ['cancelled', 'returned']);

                if (!$isOldInactive && $isNewInactive) {
                    // Restock/Restore the stock (increase stock)
                    foreach ($order->orderItems as $item) {
                        $item->adjustStock($item->quantity);
                    }
                } elseif ($isOldInactive && !$isNewInactive) {
                    // Deduct the stock again (decrease stock)
                    foreach ($order->orderItems as $item) {
                        $item->adjustStock(-$item->quantity);
                    }
                }
            }
        });
    }
}
