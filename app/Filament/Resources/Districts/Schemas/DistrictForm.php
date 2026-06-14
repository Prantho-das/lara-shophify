<?php

namespace App\Filament\Resources\Districts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DistrictForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->label('District Name')
                    ->required(),
            ]);
    }
}
