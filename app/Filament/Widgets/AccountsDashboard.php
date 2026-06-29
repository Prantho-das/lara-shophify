<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AccountsDashboard extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return module('accounts');
    }

    protected function getStats(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $totalIncome = Account::where('type', 'income')->sum('balance');
        $totalExpense = Account::where('type', 'expense')->sum('balance');
        $netProfit = $totalIncome - $totalExpense;

        $monthIncome = Transaction::where('type', 'credit')
            ->whereHas('account', fn ($q) => $q->where('type', 'income'))
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $monthExpense = Transaction::where('type', 'debit')
            ->whereHas('account', fn ($q) => $q->where('type', 'expense'))
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        return [
            Stat::make('Total Income', number_format($totalIncome, 2) . ' à§³')
                ->description('All income accounts')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Expenses', number_format($totalExpense, 2) . ' à§³')
                ->description('All expense accounts')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger'),
            Stat::make('Net Profit', number_format($netProfit, 2) . ' à§³')
                ->description('Income - Expenses')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color($netProfit >= 0 ? 'success' : 'danger'),
            Stat::make('This Month Income', number_format($monthIncome, 2) . ' à§³')
                ->description($startOfMonth->format('M Y'))
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),
            Stat::make('This Month Expenses', number_format($monthExpense, 2) . ' à§³')
                ->description($startOfMonth->format('M Y'))
                ->descriptionIcon('heroicon-o-calendar')
                ->color('warning'),
        ];
    }
}
