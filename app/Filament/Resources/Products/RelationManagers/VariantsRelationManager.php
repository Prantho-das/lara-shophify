<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\KeyValue::make('attribute_values')
                    ->keyLabel('Attribute (e.g. Size)')
                    ->valueLabel('Value (e.g. XL)')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('sku')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),
                TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('৳'),
                TextInput::make('stock_quantity')
                    ->numeric()
                    ->default(0)
                    ->required(),
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                TextColumn::make('attribute_values')->json(),
                TextColumn::make('sku')->searchable(),
                TextColumn::make('price')->money('bdt'),
                TextColumn::make('stock_quantity')->numeric(),
                \Filament\Tables\Columns\ToggleColumn::make('is_active'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
