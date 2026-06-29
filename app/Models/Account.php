<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'asset' => 'Asset',
            'liability' => 'Liability',
            'income' => 'Income',
            'expense' => 'Expense',
            'equity' => 'Equity',
            default => $this->type,
        };
    }

    public function credit(float $amount, ?string $description = null, $reference = null): void
    {
        $this->increment('balance', $amount);
        Transaction::create([
            'account_id' => $this->id,
            'type' => 'credit',
            'amount' => $amount,
            'description' => $description,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference?->id,
            'date' => now()->toDateString(),
            'created_by' => auth()->id(),
        ]);
    }

    public function debit(float $amount, ?string $description = null, $reference = null): void
    {
        $this->decrement('balance', $amount);
        Transaction::create([
            'account_id' => $this->id,
            'type' => 'debit',
            'amount' => $amount,
            'description' => $description,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference?->id,
            'date' => now()->toDateString(),
            'created_by' => auth()->id(),
        ]);
    }
}
