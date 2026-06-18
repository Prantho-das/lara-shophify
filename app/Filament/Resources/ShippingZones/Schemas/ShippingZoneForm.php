<?php

namespace App\Filament\Resources\ShippingZones\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShippingZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('৳'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
