<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name')
                    ->placeholder('Select Parent Category')
                    ->nullable()
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image') ->disk('public')
                    ->image(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
                \Filament\Forms\Components\Select::make('discount_type')
                    ->options([
                        'percent' => 'Percentage (%)',
                        'fixed' => 'Fixed Amount (৳)',
                    ])
                    ->placeholder('No Discount')
                    ->nullable()
                    ->live(),
                TextInput::make('discount_value')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => filled($get('discount_type')))
                    ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => filled($get('discount_type'))),
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
            ]);
    }
}
