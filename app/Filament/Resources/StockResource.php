<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Models\Stock;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 52;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $modelLabel = 'Stock Level';

    protected static ?string $modelLabelPlural = 'Stock Levels';

    public static function shouldRegisterNavigation(): bool
    {
        return module('inventory');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn ($set, $state) => $set('variant_id', null)),
                Forms\Components\Select::make('variant_id')
                    ->label('Variant')
                    ->options(function ($get) {
                        $product = \App\Models\Product::find($get('product_id'));
                        if (! $product) {
                            return [];
                        }
                        return $product->variants->pluck(
                            fn ($v) => implode(' / ', array_values($v->attribute_values ?? [])),
                            'id'
                        );
                    })
                    ->nullable()
                    ->searchable(),
                Forms\Components\Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0),
                Forms\Components\TextInput::make('reserved_quantity')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Quantity reserved for pending orders.'),
                Forms\Components\TextInput::make('low_stock_threshold')
                    ->numeric()
                    ->default(5)
                    ->minValue(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('reserved_quantity')
                    ->label('Reserved')
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('Available')
                    ->state(fn (Stock $record) => $record->quantity - $record->reserved_quantity)
                    ->color(fn (Stock $record) => ($record->quantity - $record->reserved_quantity) <= $record->low_stock_threshold ? 'danger' : 'success')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('low_stock_threshold')
                    ->label('Threshold'),
                Tables\Columns\IconColumn::make('is_low_stock')
                    ->label('Low Stock')
                    ->boolean()
                    ->state(fn (Stock $record) => $record->quantity <= $record->low_stock_threshold),
            ])
            ->defaultSort('quantity', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->label('Warehouse'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock Only')
                    ->query(fn ($query) => $query->whereColumn('quantity', '<=', 'low_stock_threshold')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
