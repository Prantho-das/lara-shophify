<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResellerPrice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'custom_price' => 'decimal:2',
    ];

    public function resellerProfile()
    {
        return $this->belongsTo(ResellerProfile::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
