<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResellerProfileResource\Pages;
use App\Models\ResellerProfile;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ResellerProfileResource extends Resource
{
    protected static ?string $model = ResellerProfile::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 60;

    protected static string|\UnitEnum|null $navigationGroup = 'Reseller';

    protected static ?string $modelLabel = 'Reseller Profile';

    protected static ?string $modelLabelPlural = 'Reseller Profiles';

    public static function shouldRegisterNavigation(): bool
    {
        return module('reseller');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('tier')
                    ->options([
                        'bronze' => 'Bronze',
                        'silver' => 'Silver',
                        'gold' => 'Gold',
                        'platinum' => 'Platinum',
                    ])
                    ->default('bronze')
                    ->required(),
                Forms\Components\TextInput::make('discount_percent')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
                Forms\Components\Toggle::make('custom_price_enabled')
                    ->default(false)
                    ->helperText('Allow setting custom prices per product.'),
                Forms\Components\Textarea::make('notes')
                    ->rows(3),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tier')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bronze' => 'gray',
                        'silver' => 'slate',
                        'gold' => 'warning',
                        'platinum' => 'success',
                    }),
                Tables\Columns\TextColumn::make('discount_percent')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\SelectFilter::make('tier')
                    ->options([
                        'bronze' => 'Bronze',
                        'silver' => 'Silver',
                        'gold' => 'Gold',
                        'platinum' => 'Platinum',
                    ]),
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
            'index' => Pages\ListResellerProfiles::route('/'),
            'create' => Pages\CreateResellerProfile::route('/create'),
            'edit' => Pages\EditResellerProfile::route('/{record}/edit'),
        ];
    }
}
