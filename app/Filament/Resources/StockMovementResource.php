<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use Filament\Infolists;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?int $navigationSort = 53;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $modelLabel = 'Stock Movement';

    protected static ?string $modelLabelPlural = 'Stock Movements';

    public static function shouldRegisterNavigation(): bool
    {
        return module('inventory');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock.product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'transfer' => 'info',
                        'adjustment' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('fromWarehouse.name')
                    ->label('From')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('toWarehouse.name')
                    ->label('To')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('By')
                    ->placeholder('System'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'in' => 'Stock In',
                        'out' => 'Stock Out',
                        'transfer' => 'Transfer',
                        'adjustment' => 'Adjustment',
                    ]),
                Tables\Filters\SelectFilter::make('stock_id')
                    ->label('Product')
                    ->relationship('stock.product', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Infolists\Components\Section::make('Movement Details')->schema([
                Infolists\Components\TextEntry::make('created_at')->dateTime('M d, Y H:i:s'),
                Infolists\Components\TextEntry::make('type')->badge(),
                Infolists\Components\TextEntry::make('quantity'),
                Infolists\Components\TextEntry::make('stock.product.name')->label('Product'),
                Infolists\Components\TextEntry::make('fromWarehouse.name')->label('From Warehouse'),
                Infolists\Components\TextEntry::make('toWarehouse.name')->label('To Warehouse'),
                Infolists\Components\TextEntry::make('notes'),
                Infolists\Components\TextEntry::make('creator.name')->label('Created By'),
            ])->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
            'view' => Pages\ViewStockMovement::route('/{record}'),
        ];
    }
}
