<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $guarded = [];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function locationRates()
    {
        return $this->hasMany(LocationRate::class);
    }
}
