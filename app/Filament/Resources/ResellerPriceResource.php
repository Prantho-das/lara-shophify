<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResellerPriceResource\Pages;
use App\Models\ResellerPrice;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ResellerPriceResource extends Resource
{
    protected static ?string $model = ResellerPrice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?int $navigationSort = 61;

    protected static string|\UnitEnum|null $navigationGroup = 'Reseller';

    protected static ?string $modelLabel = 'Reseller Price';

    protected static ?string $modelLabelPlural = 'Reseller Prices';

    public static function shouldRegisterNavigation(): bool
    {
        return module('reseller');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('reseller_profile_id')
                    ->relationship('resellerProfile.user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('variant_id')
                    ->label('Variant UUID')
                    ->nullable()
                    ->helperText('Leave empty for product-level pricing.'),
                Forms\Components\TextInput::make('custom_price')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->prefix('৳'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('resellerProfile.user.name')
                    ->label('Reseller')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('variant_id')
                    ->label('Variant')
                    ->placeholder('All variants'),
                Tables\Columns\TextColumn::make('custom_price')
                    ->money('BDT')
                    ->weight('bold'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListResellerPrices::route('/'),
            'create' => Pages\CreateResellerPrice::route('/create'),
            'edit' => Pages\EditResellerPrice::route('/{record}/edit'),
        ];
    }
}
