<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('location'),
            ]);
    }
}
