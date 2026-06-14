<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected ?string $heading = 'Orders Trend (Last 30 Days)';

    protected function getData(): array
    {
        $data = collect(range(29, 0))->mapWithKeys(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            $count = \App\Models\Order::whereDate('created_at', $date)->count();
            return [$date => $count];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Orders Placed',
                    'data' => $data->values()->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => 'start',
                ],
            ],
            'labels' => $data->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->format('M d'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
