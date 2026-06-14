<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('discount_type')
                    ->required()
                    ->default('percent'),
                TextInput::make('discount_value')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('start_date'),
                DateTimePicker::make('end_date'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
