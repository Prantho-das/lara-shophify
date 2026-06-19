<x-filament-panels::page>
     @vite('resources/css/app.css')
     <style>
         /* Reset Tailwind Forms override on Filament inputs */
         .fi-input-wrapper input,
         .fi-input-wrapper select,
         .fi-input-wrapper textarea {
             border: none !important;
             background-color: transparent !important;
             box-shadow: none !important;
             outline: none !important;
             --tw-ring-color: transparent !important;
             --tw-ring-shadow: none !important;
             --tw-shadow: none !important;
             padding-top: 0px !important;
             padding-bottom: 0px !important;
         }
     </style>
    <div class="space-y-6">

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
            <div class="group relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="text-sm font-semibold text-gray-600">Total Net Sales Revenue</div>
                <div class="mt-2 text-3xl font-black tracking-tight text-emerald-600">৳{{ number_format($report['total_sales'], 2) }}</div>
                <div class="text-[10px] text-gray-500 mt-2">From {{ $report['start_date'] }} to {{ $report['end_date'] }}</div>
                <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5 text-emerald-600" />
                </div>
            </div>

            <!-- Metric 2: Total Orders -->
            <div class="group relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="text-sm font-semibold text-gray-600">Total Orders Placed</div>
                <div class="mt-2 text-3xl font-black tracking-tight text-primary-600">{{ $report['total_orders'] }}</div>
                <div class="text-[10px] text-gray-500 mt-2">All orders including pending / processing</div>
                <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <x-filament::icon icon="heroicon-o-shopping-cart" class="h-5 w-5 text-primary-600" />
                </div>
            </div>

            <!-- Metric 3: Average Order Value -->
            <div class="group relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="text-sm font-semibold text-gray-600">Average Transaction Value</div>
                <div class="mt-2 text-3xl font-black tracking-tight text-amber-600">৳{{ number_format($report['avg_order_value'], 2) }}</div>
                <div class="text-[10px] text-gray-500 mt-2">Average spent value per customer checkout</div>
                <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <x-filament::icon icon="heroicon-o-currency-dollar" class="h-5 w-5 text-amber-600" />
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Table 1: Top Selling Products -->
            <div class="fi-ta-ctn !flex !flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 transition-all duration-300 hover:shadow-lg">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Top 5 Best Selling Products</h3>
                    <p class="text-xs text-gray-600 mt-1">Products with highest sales volume</p>
                </div>
                <div class="flex-grow">
                    @if(count($report['top_products']) > 0)
                        <div class="overflow-x-auto">
                            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-start font-semibold text-gray-900">Product Name</th>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-center font-semibold text-gray-900">Qty Sold</th>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-end font-semibold text-gray-900">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($report['top_products'] as $item)
                                        @if($item->product)
                                            <tr class="fi-ta-row group [@media(hover:hover)]:hover:bg-gray-50 transition-all">
                                                <td class="fi-ta-cell px-6 py-4 font-semibold text-gray-900">{{ $item->product->name }}</td>
                                                <td class="fi-ta-cell px-6 py-4 text-center font-semibold text-gray-600">{{ $item->qty_sold }} units</td>
                                                <td class="fi-ta-cell px-6 py-4 text-end font-semibold text-emerald-600">৳{{ number_format($item->revenue, 2) }}</td>
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
            <div class="fi-ta-ctn !flex !flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 transition-all duration-300 hover:shadow-lg">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Sales Breakdown by Category</h3>
                    <p class="text-xs text-gray-600 mt-1">Revenue distribution across product categories</p>
                </div>
                <div class="flex-grow">
                    @if(count($report['category_sales']) > 0)
                        <div class="overflow-x-auto">
                            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-start font-semibold text-gray-900">Category</th>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-center font-semibold text-gray-900">Items Sold</th>
                                        <th class="fi-ta-header-cell px-6 py-3.5 text-end font-semibold text-gray-900">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($report['category_sales'] as $cat)
                                        <tr class="fi-ta-row group [@media(hover:hover)]:hover:bg-gray-50 transition-all">
                                            <td class="fi-ta-cell px-6 py-4 font-semibold text-gray-900">{{ $cat->cat_name }}</td>
                                            <td class="fi-ta-cell px-6 py-4 text-center font-semibold text-gray-600">{{ $cat->qty_sold }} units</td>
                                            <td class="fi-ta-cell px-6 py-4 text-end font-semibold text-emerald-600">৳{{ number_format($cat->revenue, 2) }}</td>
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
        <div class="fi-ta-ctn !flex !flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 transition-all duration-300 hover:shadow-lg">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Payment Method Statistics</h3>
                <p class="text-xs text-gray-600 mt-1">Transaction breakdown by payment method</p>
            </div>
            @if(count($report['payment_methods']) > 0)
                <div class="overflow-x-auto">
                    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="fi-ta-header-cell px-6 py-3.5 text-start font-semibold text-gray-900">Method</th>
                                <th class="fi-ta-header-cell px-6 py-3.5 text-center font-semibold text-gray-900">Tx Count</th>
                                <th class="fi-ta-header-cell px-6 py-3.5 text-end font-semibold text-gray-900">Total Sales</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($report['payment_methods'] as $pay)
                                <tr class="fi-ta-row group [@media(hover:hover)]:hover:bg-gray-50 transition-all">
                                    <td class="fi-ta-cell px-6 py-4 font-semibold text-gray-900 uppercase">{{ $pay->payment_method }}</td>
                                    <td class="fi-ta-cell px-6 py-4 text-center font-semibold text-gray-600">{{ $pay->count }} orders</td>
                                    <td class="fi-ta-cell px-6 py-4 text-end font-semibold text-emerald-600">৳{{ number_format($pay->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center text-xs text-gray-400">No payments transaction recorded.</div>
            @endif
        </div>
</x-filament-panels::page>
