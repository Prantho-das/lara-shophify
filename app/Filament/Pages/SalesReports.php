<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Order;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;

class SalesReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';
        protected static ?int $navigationSort = 90;
    protected static ?string $title = 'Sales & Business Reports';

    protected string $view = 'filament.pages.sales-reports';

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill([
            'startDate' => now()->startOfMonth()->format('Y-m-d'),
            'endDate' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('startDate')
                    ->label('Start Date')
                    ->default(now()->startOfMonth())
                    ->live(),
                DatePicker::make('endDate')
                    ->label('End Date')
                    ->default(now())
                    ->live(),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function getReportData()
    {
        $formData = $this->form->getState();
        $start = !empty($formData['startDate']) ? Carbon::parse($formData['startDate'])->startOfDay() : now()->startOfMonth()->startOfDay();
        $end = !empty($formData['endDate']) ? Carbon::parse($formData['endDate'])->endOfDay() : now()->endOfDay();

        $ordersQuery = Order::whereBetween('created_at', [$start, $end]);
        
        $totalOrders = $ordersQuery->count();
        // Exclude cancelled and failed payments from active revenue
        $totalSales = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
            
        $avgOrderValue = $totalOrders > 0 ? ($totalSales / $totalOrders) : 0;
        
        $paymentMethods = Order::whereBetween('created_at', [$start, $end])
            ->selectRaw('payment_method, count(*) as count, sum(total_amount) as total')
            ->groupBy('payment_method')
            ->get();

        // Top selling products
        $topProducts = \App\Models\OrderItem::whereBetween('order_items.created_at', [$start, $end])
            ->selectRaw('product_id, sum(quantity) as qty_sold, sum(total) as revenue')
            ->groupBy('product_id')
            ->with('product')
            ->orderBy('qty_sold', 'desc')
            ->limit(5)
            ->get();

        // Sales by category
        $categorySales = \App\Models\OrderItem::whereBetween('order_items.created_at', [$start, $end])
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as cat_name, sum(order_items.quantity) as qty_sold, sum(order_items.total) as revenue')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('revenue', 'desc')
            ->get();

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'avg_order_value' => $avgOrderValue,
            'payment_methods' => $paymentMethods,
            'top_products' => $topProducts,
            'category_sales' => $categorySales,
            'start_date' => $start->format('M d, Y'),
            'end_date' => $end->format('M d, Y'),
        ];
    }
}
