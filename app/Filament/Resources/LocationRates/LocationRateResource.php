<?php

namespace App\Filament\Resources\LocationRates;

use App\Filament\Resources\LocationRates\Pages\ManageLocationRates;
use App\Models\LocationRate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationRateResource extends Resource
{
    protected static ?string $model = LocationRate::class;

        protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?int $navigationSort = 150;
    protected static ?string $title = 'Location Shipping Rates';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('country_id')
                    ->label('Country')
                    ->options(\App\Models\Country::pluck('name', 'id')->toArray())
                    ->required()
                    ->reactive()
                    ->searchable()
                    ->afterStateHydrated(function (callable $set, $state, $record) {
                        if ($record && $record->district) {
                            $set('country_id', $record->district->country_id);
                        }
                    })
                    ->createOptionForm([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required()
                            ->unique(table: \App\Models\Country::class, column: 'name'),
                        \Filament\Forms\Components\TextInput::make('code')
                            ->maxLength(10)
                            ->placeholder('e.g. BD, IN'),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return \App\Models\Country::create($data)->id;
                    }),
                \Filament\Forms\Components\Select::make('district_id')
                    ->label('District')
                    ->options(fn (callable $get) => \App\Models\District::where('country_id', $get('country_id'))->pluck('name', 'id')->toArray())
                    ->required()
                    ->searchable()
                    ->disabled(fn (callable $get) => !$get('country_id'))
                    ->createOptionForm([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data, callable $get) {
                        return \App\Models\District::create([
                            'country_id' => $get('country_id'),
                            'name' => $data['name'],
                        ])->id;
                    }),
                TextInput::make('area')
                    ->label('Area / Police Station / Post Code')
                    ->placeholder('e.g. Dhanmondi, Agrabad')
                    ->required()
                    ->maxLength(255),
                TextInput::make('charge')
                    ->label('Shipping Charge (৳)')
                    ->required()
                    ->numeric()
                    ->prefix('৳')
                    ->default(60.00),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('district.country.name')
                    ->label('Country')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('district.name')
                    ->label('District')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('area')
                    ->searchable(),
                TextColumn::make('charge')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLocationRates::route('/'),
        ];
    }
}
