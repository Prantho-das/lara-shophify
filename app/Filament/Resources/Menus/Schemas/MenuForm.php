<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\Select::make('location')
                    ->label('Menu Location')
                    ->options([
                        'header' => 'Header Menu',
                        'footer' => 'Footer Menu',
                    ])
                    ->required(),
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(4)->schema([
                            TextInput::make('title')
                                ->required()
                                ->columnSpan(1),
                            \Filament\Forms\Components\Select::make('type')
                                ->options([
                                    'custom' => 'Custom URL',
                                    'category' => 'Category Link',
                                    'brand' => 'Brand Link',
                                ])
                                ->default('custom')
                                ->required()
                                ->reactive()
                                ->columnSpan(1),
                            TextInput::make('url')
                                ->label('URL Link')
                                ->nullable()
                                ->visible(fn($get) => $get('type') === 'custom' || !$get('type'))
                                ->required(fn($get) => $get('type') === 'custom')
                                ->columnSpan(2),
                            \Filament\Forms\Components\Select::make('category_id')
                                ->label('Select Category')
                                ->options(fn() => \App\Models\Category::pluck('name', 'id')->toArray())
                                ->nullable()
                                ->visible(fn($get) => $get('type') === 'category')
                                ->required(fn($get) => $get('type') === 'category')
                                ->columnSpan(2),
                            \Filament\Forms\Components\Select::make('brand_id')
                                ->label('Select Brand')
                                ->options(fn() => \App\Models\Brand::pluck('name', 'id')->toArray())
                                ->nullable()
                                ->visible(fn($get) => $get('type') === 'brand')
                                ->required(fn($get) => $get('type') === 'brand')
                                ->columnSpan(2),
                        ])
                    ])
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}
