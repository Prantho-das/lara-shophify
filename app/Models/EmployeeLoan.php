<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLoan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'monthly_deduction' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function deduct(float $amount): void
    {
        $this->decrement('remaining_amount', $amount);
        if ($this->remaining_amount <= 0) {
            $this->update(['status' => 'completed', 'remaining_amount' => 0]);
        }
    }
}
