<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\PayrollRecord;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PayrollResource extends Resource
{
    protected static ?string $model = PayrollRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?int $navigationSort = 82;

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?string $modelLabel = 'Payroll Record';

    protected static ?string $modelLabelPlural = 'Payroll Records';

    public static function shouldRegisterNavigation(): bool
    {
        return module('payroll');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Section::make('Employee & Period')->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'name')
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($set, $get, $state) {
                        $employee = Employee::find($state);
                        if ($employee) {
                            $set('basic_salary', $employee->salary);
                        }
                    }),
                Forms\Components\DatePicker::make('month')
                    ->required()
                    ->displayFormat('Y-m')
                    ->label('Pay Period'),
            ])->columns(2),
            Forms\Components\Section::make('Attendance')->schema([
                Forms\Components\TextInput::make('working_days')
                    ->numeric()
                    ->required()
                    ->default(now()->daysInMonth)
                    ->minValue(1)
                    ->maxValue(31),
                Forms\Components\TextInput::make('present_days')
                    ->numeric()
                    ->required()
                    ->default(now()->day)
                    ->minValue(0),
            ])->columns(2),
            Forms\Components\Section::make('Salary')->schema([
                Forms\Components\TextInput::make('basic_salary')
                    ->numeric()
                    ->required()
                    ->prefix('৳'),
                Forms\Components\Repeater::make('allowances')
                    ->label('Allowances')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('amount')->numeric()->required()->minValue(0),
                    ])->columns(2)->defaultItems(0)->collapsible(),
                Forms\Components\Repeater::make('deductions')
                    ->label('Deductions')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('amount')->numeric()->required()->minValue(0),
                    ])->columns(2)->defaultItems(0)->collapsible(),
                Forms\Components\TextInput::make('loan_deduction')
                    ->numeric()
                    ->default(0)
                    ->prefix('৳')
                    ->helperText('Auto-calculated from active loans if empty.'),
                Forms\Components\TextInput::make('tax_deduction')
                    ->numeric()
                    ->default(0)
                    ->prefix('৳'),
            ])->columns(2),
            Forms\Components\Section::make('Result')->schema([
                Forms\Components\TextInput::make('net_salary')
                    ->numeric()
                    ->readOnly()
                    ->prefix('৳')
                    ->helperText('Auto-calculated on save.'),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('draft')
                    ->required(),
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
                Tables\Columns\TextColumn::make('month')
                    ->date('M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->money('BDT'),
                Tables\Columns\TextColumn::make('net_salary')
                    ->money('BDT')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime('M d, Y')
                    ->placeholder('—'),
            ])
            ->defaultSort('month', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
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
            'index' => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
