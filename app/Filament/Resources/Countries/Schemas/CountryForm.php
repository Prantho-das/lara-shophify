<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('code')
                    ->maxLength(10)
                    ->placeholder('e.g. BD, IN'),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
