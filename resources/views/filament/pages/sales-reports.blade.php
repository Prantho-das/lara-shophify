<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Date Filters Card -->
        <div class="p-6 bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm">
            <form class="space-y-4">
                {{ $this->form }}
            </form>
        </div>

        @php
            $report = $this->getReportData();
        @endphp

        <!-- Metrics Overview Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Metric 1: Total Revenue -->
            <div class="p-6 bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm flex flex-col justify-between">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Net Sales Revenue</span>
                <span class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-500 mt-2">৳{{ number_format($report['total_sales'], 2) }}</span>
                <span class="text-[10px] text-gray-400 mt-1">From {{ $report['start_date'] }} to {{ $report['end_date'] }}</span>
            </div>

            <!-- Metric 2: Total Orders -->
            <div class="p-6 bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm flex flex-col justify-between">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Orders Placed</span>
                <span class="text-3xl font-extrabold text-primary-600 dark:text-primary-500 mt-2">{{ $report['total_orders'] }}</span>
                <span class="text-[10px] text-gray-400 mt-1">All orders including pending / processing</span>
            </div>

            <!-- Metric 3: Average Order Value -->
            <div class="p-6 bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm flex flex-col justify-between">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Average Transaction Value</span>
                <span class="text-3xl font-extrabold text-amber-600 dark:text-amber-500 mt-2">৳{{ number_format($report['avg_order_value'], 2) }}</span>
                <span class="text-[10px] text-gray-400 mt-1">Average spent value per customer checkout</span>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Table 1: Top Selling Products -->
            <div class="bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-sm font-extrabold text-gray-900 dark:text-white uppercase tracking-wider">Top 5 Best Selling Products</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800 flex-grow">
                    @if(count($report['top_products']) > 0)
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">
                                    <th class="px-6 py-3">Product Name</th>
                                    <th class="px-6 py-3 text-center">Qty Sold</th>
                                    <th class="px-6 py-3 text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($report['top_products'] as $item)
                                    @if($item->product)
                                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/20">
                                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                                            <td class="px-6 py-4 text-center font-extrabold text-gray-500 dark:text-gray-400">{{ $item->qty_sold }} units</td>
                                            <td class="px-6 py-4 text-right font-extrabold text-emerald-600 dark:text-emerald-500">৳{{ number_format($item->revenue, 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-6 text-center text-xs text-gray-400">No sales data found for this range.</div>
                    @endif
                </div>
            </div>

            <!-- Table 2: Sales By Category -->
            <div class="bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-sm font-extrabold text-gray-900 dark:text-white uppercase tracking-wider">Sales Breakdown by Category</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800 flex-grow">
                    @if(count($report['category_sales']) > 0)
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">
                                    <th class="px-6 py-3">Category</th>
                                    <th class="px-6 py-3 text-center">Items Sold</th>
                                    <th class="px-6 py-3 text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($report['category_sales'] as $cat)
                                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/20">
                                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $cat->cat_name }}</td>
                                        <td class="px-6 py-4 text-center font-extrabold text-gray-500 dark:text-gray-400">{{ $cat->qty_sold }} units</td>
                                        <td class="px-6 py-4 text-right font-extrabold text-emerald-600 dark:text-emerald-500">৳{{ number_format($cat->revenue, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-6 text-center text-xs text-gray-400">No category sales data found.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table 3: Payment Method Distribution -->
        <div class="bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white uppercase tracking-wider">Payment Method Statistics</h3>
            </div>
            @if(count($report['payment_methods']) > 0)
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">
                            <th class="px-6 py-3">Method</th>
                            <th class="px-6 py-3 text-center">Tx Count</th>
                            <th class="px-6 py-3 text-right font-bold">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($report['payment_methods'] as $pay)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/20">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white uppercase">{{ $pay->payment_method }}</td>
                                <td class="px-6 py-4 text-center font-extrabold text-gray-500 dark:text-gray-400">{{ $pay->count }} orders</td>
                                <td class="px-6 py-4 text-right font-extrabold text-emerald-600 dark:text-emerald-500">৳{{ number_format($pay->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-6 text-center text-xs text-gray-400">No payments transaction recorded.</div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
