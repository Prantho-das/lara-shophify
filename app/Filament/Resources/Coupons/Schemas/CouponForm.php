<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true),
                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'fixed' => 'Fixed Discount (৳)',
                        'percent' => 'Percentage Discount (%)',
                        'free_shipping' => 'Free Shipping',
                    ])
                    ->required()
                    ->default('fixed')
                    ->reactive(),
                TextInput::make('discount_value')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->disabled(fn ($get) => $get('type') === 'free_shipping')
                    ->dehydrated(true)
                    ->prefix('৳'),
                TextInput::make('min_spend')
                    ->numeric()
                    ->prefix('৳'),
                DateTimePicker::make('expiry_date'),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required()
                    ->default('active'),
            ]);
    }
}
