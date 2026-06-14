<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Order;
use App\Models\User;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $revenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $ordersCount = Order::count();
        $averageOrderValue = $ordersCount > 0 ? ($revenue / $ordersCount) : 0;
        $customersCount = User::count();

        return [
            Stat::make('Total Revenue', '৳' . number_format($revenue, 2))
                ->description('All completed sales (excl. cancelled)')
                ->descriptionIcon('heroicon-m-currency-bangladeshi')
                ->color('success'),
            Stat::make('Total Orders', $ordersCount)
                ->description('Total order transactions placed')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            Stat::make('Average Order Value', '৳' . number_format($averageOrderValue, 2))
                ->description('Average amount spent per order')
                ->descriptionIcon('heroicon-m-presentation-chart-line')
                ->color('warning'),
            Stat::make('Total Customers', $customersCount)
                ->description('Registered user accounts')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
