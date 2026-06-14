<x-filament-panels::page>
    <div class="space-y-6">
        @php
            $totalItems = count($stocks);
            $lowStockCount = 0;
            $outOfStockCount = 0;
            foreach ($stocks as $item) {
                if ($item['current_stock'] === 0) {
                    $outOfStockCount++;
                } elseif ($item['current_stock'] <= $item['low_threshold']) {
                    $lowStockCount++;
                }
            }
        @endphp

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm flex flex-col justify-between">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Tracked Items</span>
                <span class="text-3xl font-extrabold text-gray-900 dark:text-white mt-2">{{ $totalItems }}</span>
                <span class="text-[10px] text-gray-400 mt-1">Total count of products and unique variants</span>
            </div>

            <div class="p-6 bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm flex flex-col justify-between">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Low Stock Alert</span>
                <span class="text-3xl font-extrabold text-amber-600 dark:text-amber-500 mt-2">{{ $lowStockCount }}</span>
                <span class="text-[10px] text-gray-400 mt-1">Items below standard threshold (qty <= 5 or 10)</span>
            </div>

            <div class="p-6 bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm flex flex-col justify-between">
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Out of Stock</span>
                <span class="text-3xl font-extrabold text-red-600 dark:text-red-500 mt-2">{{ $outOfStockCount }}</span>
                <span class="text-[10px] text-gray-400 mt-1">Items with zero inventory count left</span>
            </div>
        </div>

        <!-- Inventory List Card -->
        <div class="bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-gray-900 dark:text-white uppercase tracking-wider">Inventory List & Stock Levels</h3>
                <button wire:click="loadStocks" class="text-xs font-bold text-primary-600 dark:text-primary-500 hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-arrows-rotate"></i> Refresh
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">
                            <th class="px-6 py-4">Product details</th>
                            <th class="px-6 py-4">Variant Option</th>
                            <th class="px-6 py-4">SKU / Barcode</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Current Stock</th>
                            <th class="px-6 py-4 text-right">Stock Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($stocks as $key => $item)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/20 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $item['product_name'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $item['type'] === 'variant' ? 'bg-indigo-50 border-indigo-100 text-indigo-700 dark:bg-indigo-950/20 dark:border-indigo-900 dark:text-indigo-400' : 'bg-gray-50 border-gray-100 text-gray-600 dark:bg-gray-800/40 dark:border-gray-800 dark:text-gray-400' }}">
                                        {{ $item['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">{{ $item['sku'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($item['current_stock'] === 0)
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-800 border border-red-200 dark:bg-red-950/30 dark:border-red-900 dark:text-red-400">Out of Stock</span>
                                    @elseif($item['current_stock'] <= $item['low_threshold'])
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950/30 dark:border-amber-900 dark:text-amber-400">Low Stock</span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/30 dark:border-emerald-900 dark:text-emerald-400">In Stock</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-black text-gray-900 dark:text-white text-sm">
                                    {{ $item['current_stock'] }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2" x-data="{ editing: false }">
                                        <input 
                                            type="number" 
                                            wire:model="stocks.{{ $key }}.new_stock" 
                                            class="w-16 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded px-2.5 py-1.5 text-xs text-center focus:outline-none focus:border-primary-500"
                                            min="0"
                                        >
                                        <button 
                                            wire:click="updateStock('{{ $key }}')" 
                                            wire:loading.attr="disabled"
                                            class="px-3 py-1.5 bg-primary-600 hover:bg-primary-500 text-white rounded text-xs font-bold transition-colors shadow-sm cursor-pointer"
                                        >
                                            Update
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
