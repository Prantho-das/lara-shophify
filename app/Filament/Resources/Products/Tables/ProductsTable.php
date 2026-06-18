<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('base_price')
                    ->formatStateUsing(fn ($state) => (\App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '৳') . ' ' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('compare_price')
                    ->formatStateUsing(fn ($state) => $state ? ((\App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '৳') . ' ' . number_format($state, 2)) : '-')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('has_variants')
                    ->boolean(),
                TextColumn::make('meta_title')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                IconColumn::make('featured')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
