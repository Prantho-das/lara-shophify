<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\PayrollRecord;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PayrollDashboard extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return module('payroll');
    }

    protected function getStats(): array
    {
        $totalEmployees = Employee::active()->count();
        $monthPayroll = PayrollRecord::where('month', now()->startOfMonth())
            ->where('status', 'paid')
            ->sum('net_salary');
        $pendingPayroll = PayrollRecord::where('month', now()->startOfMonth())
            ->where('status', 'draft')
            ->sum('net_salary');
        $totalPaidThisYear = PayrollRecord::where('status', 'paid')
            ->whereYear('month', now()->year)
            ->sum('net_salary');

        return [
            Stat::make('Active Employees', $totalEmployees)
                ->description('Currently active')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),
            Stat::make('Paid This Month', number_format($monthPayroll, 2) . ' à§³')
                ->description(now()->startOfMonth()->format('M Y'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Pending This Month', number_format($pendingPayroll, 2) . ' à§³')
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Total Paid ' . now()->year, number_format($totalPaidThisYear, 2) . ' à§³')
                ->description('Year to date')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),
        ];
    }
}
