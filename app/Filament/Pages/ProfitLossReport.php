<?php

namespace App\Filament\Pages;

use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class ProfitLossReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 73;
    protected static string | \UnitEnum | null $navigationGroup = 'Accounts';
    protected static ?string $title = 'Profit & Loss Report';

    protected string $view = 'filament.pages.profit-loss-report';

    public ?array $data = [];
    public array $reportData = [];

    public static function canView(): bool
    {
        return module('accounts');
    }

    public function mount(): void
    {
        $this->form->fill([
            'startDate' => now()->startOfMonth()->format('Y-m-d'),
            'endDate' => now()->format('Y-m-d'),
        ]);
        $this->loadReport();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\DatePicker::make('startDate')
                    ->label('Start Date')
                    ->default(now()->startOfMonth())
                    ->live(),
                Forms\Components\DatePicker::make('endDate')
                    ->label('End Date')
                    ->default(now())
                    ->live(),
                Forms\Components\Button::make('load')
                    ->label('Load Report')
                    ->action(fn () => $this->loadReport()),
            ])
            ->statePath('data')
            ->columns(3);
    }

    public function loadReport(): void
    {
        $formData = $this->form->getState();
        $start = !empty($formData['startDate']) ? Carbon::parse($formData['startDate'])->startOfDay() : now()->startOfMonth()->startOfDay();
        $end = !empty($formData['endDate']) ? Carbon::parse($formData['endDate'])->endOfDay() : now()->endOfDay();

        $incomeTransactions = Transaction::where('type', 'credit')
            ->whereHas('account', fn ($q) => $q->where('type', 'income'))
            ->whereBetween('date', [$start, $end])
            ->with('account')
            ->get();

        $expenseTransactions = Transaction::where('type', 'debit')
            ->whereHas('account', fn ($q) => $q->where('type', 'expense'))
            ->whereBetween('date', [$start, $end])
            ->with('account')
            ->get();

        $this->reportData = [
            'income' => [
                'transactions' => $incomeTransactions,
                'total' => $incomeTransactions->sum('amount'),
            ],
            'expense' => [
                'transactions' => $expenseTransactions,
                'total' => $expenseTransactions->sum('amount'),
            ],
            'net_profit' => $incomeTransactions->sum('amount') - $expenseTransactions->sum('amount'),
            'start_date' => $start->format('M d, Y'),
            'end_date' => $end->format('M d, Y'),
        ];
    }
}
