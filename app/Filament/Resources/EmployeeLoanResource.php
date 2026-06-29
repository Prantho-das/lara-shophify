<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeLoanResource\Pages;
use App\Models\EmployeeLoan;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeLoanResource extends Resource
{
    protected static ?string $model = EmployeeLoan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 83;

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $modelLabel = 'Employee Loan';

    protected static ?string $modelLabelPlural = 'Employee Loans';

    public static function shouldRegisterNavigation(): bool
    {
        return module('payroll');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->prefix('৳'),
                Forms\Components\TextInput::make('monthly_deduction')
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->prefix('৳'),
                Forms\Components\TextInput::make('remaining_amount')
                    ->numeric()
                    ->readOnly()
                    ->prefix('৳')
                    ->helperText('Auto-set to loan amount on creation.'),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->rows(3),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('BDT')
                    ->sortable(),
                Tables\Columns\TextColumn::make('monthly_deduction')
                    ->money('BDT'),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->money('BDT')
                    ->color(fn (EmployeeLoan $record) => $record->remaining_amount > 0 ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'warning',
                        'completed' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
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
            'index' => Pages\ListEmployeeLoans::route('/'),
            'create' => Pages\CreateEmployeeLoan::route('/create'),
            'edit' => Pages\EditEmployeeLoan::route('/{record}/edit'),
        ];
    }
}
