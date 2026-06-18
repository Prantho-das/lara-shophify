<x-filament-panels::page>
    <div class="space-y-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sales Reports</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View and analyze your sales performance metrics</p>
        </div>

        <!-- Date Filters Card -->
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
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
            <div class="group relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col justify-between transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Net Sales Revenue</div>
                <div class="mt-2 text-3xl font-black tracking-tight text-emerald-600 dark:text-emerald-500">৳{{ number_format($report['total_sales'], 2) }}</div>
                <div class="text-[10px] text-gray-400 mt-2">From {{ $report['start_date'] }} to {{ $report['end_date'] }}</div>
                <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5 text-emerald-500" />
                </div>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>

            <!-- Metric 2: Total Orders -->
            <div class="group relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col justify-between transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Orders Placed</div>
                <div class="mt-2 text-3xl font-black tracking-tight text-primary-600 dark:text-primary-500">{{ $report['total_orders'] }}</div>
                <div class="text-[10px] text-gray-400 mt-2">All orders including pending / processing</div>
                <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <x-filament::icon icon="heroicon-o-shopping-cart" class="h-5 w-5 text-primary-500" />
                </div>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-primary-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>

            <!-- Metric 3: Average Order Value -->
            <div class="group relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col justify-between transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">Average Transaction Value</div>
                <div class="mt-2 text-3xl font-black tracking-tight text-amber-600 dark:text-amber-500">৳{{ number_format($report['avg_order_value'], 2) }}</div>
                <div class="text-[10px] text-gray-400 mt-2">Average spent value per customer checkout</div>
                <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <x-filament::icon icon="heroicon-o-currency-dollar" class="h-5 w-5 text-amber-500" />
                </div>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Table 1: Top Selling Products -->
            <div class="fi-ta-ctn !flex !flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 transition-all duration-300 hover:shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-xs font-bold text-gray-950 dark:text-white uppercase tracking-wider">Top 5 Best Selling Products</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Products with highest sales volume</p>
                </div>
                <div class="flex-grow">
                    @if(count($report['top_products']) > 0)
                        <div class="overflow-x-auto">
                            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 dark:divide-gray-800 text-start text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-start font-semibold text-gray-900 dark:text-white">Product Name</th>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-center font-semibold text-gray-900 dark:text-white">Qty Sold</th>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-end font-semibold text-gray-900 dark:text-white">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    @foreach($report['top_products'] as $item)
                                        @if($item->product)
                                            <tr class="fi-ta-row group [@media(hover:hover)]:hover:bg-gradient-to-r [@media(hover:hover)]:hover:from-emerald-50/50 [@media(hover:hover)]:hover:to-transparent dark:[@media(hover:hover)]:hover:from-emerald-900/20 dark:[@media(hover:hover)]:hover:to-transparent transition-all">
                                                <td class="fi-ta-cell px-6 py-4 font-semibold text-gray-950 dark:text-white">{{ $item->product->name }}</td>
                                                <td class="fi-ta-cell px-6 py-4 text-center font-semibold text-gray-500 dark:text-gray-400">{{ $item->qty_sold }} units</td>
                                                <td class="fi-ta-cell px-6 py-4 text-end font-semibold text-emerald-600 dark:text-emerald-500">৳{{ number_format($item->revenue, 2) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-xs text-gray-400">No sales data found for this range.</div>
                    @endif
                </div>
            </div>

            <!-- Table 2: Sales By Category -->
            <div class="fi-ta-ctn !flex !flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 transition-all duration-300 hover:shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="text-xs font-bold text-gray-950 dark:text-white uppercase tracking-wider">Sales Breakdown by Category</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Revenue distribution across product categories</p>
                </div>
                <div class="flex-grow">
                    @if(count($report['category_sales']) > 0)
                        <div class="overflow-x-auto">
                            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 dark:divide-gray-800 text-start text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-start font-semibold text-gray-900 dark:text-white">Category</th>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-center font-semibold text-gray-900 dark:text-white">Items Sold</th>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-end font-semibold text-gray-900 dark:text-white">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    @foreach($report['category_sales'] as $cat)
                                        <tr class="fi-ta-row group [@media(hover:hover)]:hover:bg-gradient-to-r [@media(hover:hover)]:hover:from-amber-50/50 [@media(hover:hover)]:hover:to-transparent dark:[@media(hover:hover)]:hover:from-amber-900/20 dark:[@media(hover:hover)]:hover:to-transparent transition-all">
                                            <td class="fi-ta-cell px-6 py-4 font-semibold text-gray-950 dark:text-white">{{ $cat->cat_name }}</td>
                                            <td class="fi-ta-cell px-6 py-4 text-center font-semibold text-gray-500 dark:text-gray-400">{{ $cat->qty_sold }} units</td>
                                            <td class="fi-ta-cell px-6 py-4 text-end font-semibold text-emerald-600 dark:text-emerald-500">৳{{ number_format($cat->revenue, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-xs text-gray-400">No category sales data found.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table 3: Payment Method Distribution -->
        <div class="fi-ta-ctn !flex !flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 transition-all duration-300 hover:shadow-2xl">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                <h3 class="text-xs font-bold text-gray-950 dark:text-white uppercase tracking-wider">Payment Method Statistics</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Transaction breakdown by payment method</p>
            </div>
            @if(count($report['payment_methods']) > 0)
                <div class="overflow-x-auto">
                    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 dark:divide-gray-800 text-start text-xs">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="fi-ta-header-cell px-6 py-3.5 text-start font-semibold text-gray-900 dark:text-white">Method</th>
                                <th class="fi-ta-header-cell px-6 py-3.5 text-center font-semibold text-gray-900 dark:text-white">Tx Count</th>
                                <th class="fi-ta-header-cell px-6 py-3.5 text-end font-semibold text-gray-900 dark:text-white">Total Sales</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($report['payment_methods'] as $pay)
                                <tr class="fi-ta-row group [@media(hover:hover)]:hover:bg-gradient-to-r [@media(hover:hover)]:hover:from-primary-50/50 [@media(hover:hover)]:hover:to-transparent dark:[@media(hover:hover)]:hover:from-primary-900/20 dark:[@media(hover:hover)]:hover:to-transparent transition-all">
                                    <td class="fi-ta-cell px-6 py-4 font-semibold text-gray-950 dark:text-white uppercase">{{ $pay->payment_method }}</td>
                                    <td class="fi-ta-cell px-6 py-4 text-center font-semibold text-gray-500 dark:text-gray-400">{{ $pay->count }} orders</td>
                                    <td class="fi-ta-cell px-6 py-4 text-end font-semibold text-emerald-600 dark:text-emerald-500">৳{{ number_format($pay->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center text-xs text-gray-400">No payments transaction recorded.</div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
