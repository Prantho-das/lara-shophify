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
         <div class="mb-6">
             <h1 class="text-2xl font-bold text-gray-900">Inventory Manager</h1>
             <p class="mt-1 text-sm text-gray-600">Manage your product inventory and track stock levels</p>
         </div>

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
             <div class="group relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                 <span class="text-sm font-semibold text-gray-600">Total Tracked Items</span>
                 <span class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalItems }}</span>
                 <span class="text-[10px] text-gray-500 mt-1">Total count of products and unique variants</span>
                 <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                     <x-filament::icon icon="heroicon-o-archive-box" class="h-5 w-5 text-gray-600" />
                 </div>
             </div>

             <div class="group relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                 <span class="text-sm font-semibold text-gray-600">Low Stock Alert</span>
                 <span class="text-3xl font-extrabold text-amber-600 mt-2">{{ $lowStockCount }}</span>
                 <span class="text-[10px] text-gray-500 mt-1">Items below standard threshold</span>
                 <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                     <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5 text-amber-600" />
                 </div>
             </div>

             <div class="group relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                 <span class="text-sm font-semibold text-gray-600">Out of Stock</span>
                 <span class="text-3xl font-extrabold text-red-600 mt-2">{{ $outOfStockCount }}</span>
                 <span class="text-[10px] text-gray-500 mt-1">Items with zero inventory count left</span>
                 <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                     <x-filament::icon icon="heroicon-o-x-circle" class="h-5 w-5 text-red-600" />
                 </div>
             </div>
         </div>

         <!-- Inventory List Card -->
         <div class="fi-ta-ctn !flex !flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 transition-all duration-300 hover:shadow-lg">
             <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                 <div>
                     <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Inventory List & Stock Levels</h3>
                     <p class="text-xs text-gray-600 mt-1">Manage product inventory and update stock quantities</p>
                 </div>
                 <x-filament::button wire:click="loadStocks" size="xs" color="gray" icon="heroicon-m-arrow-path" class="transition-all duration-300 hover:rotate-180 hover:bg-gray-200">
                     Refresh
                 </x-filament::button>
             </div>
             
             <div class="overflow-x-auto">
                 <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start text-xs">
                     <thead class="bg-gray-50">
                         <tr>
                             <th class="fi-ta-header-cell px-6 py-3.5 text-start font-semibold text-gray-900">Product details</th>
                             <th class="fi-ta-header-cell px-6 py-3.5 text-start font-semibold text-gray-900">Variant Option</th>
                             <th class="fi-ta-header-cell px-6 py-3.5 text-start font-semibold text-gray-900">SKU / Barcode</th>
                             <th class="fi-ta-header-cell px-6 py-3.5 text-center font-semibold text-gray-900">Status</th>
                             <th class="fi-ta-header-cell px-6 py-3.5 text-center font-semibold text-gray-900">Current Stock</th>
                             <th class="fi-ta-header-cell px-6 py-3.5 text-end font-semibold text-gray-900">Stock Update</th>
                         </tr>
                     </thead>
                     <tbody class="divide-y divide-gray-200">
                         @foreach($stocks as $key => $item)
                             <tr class="fi-ta-row group [@media(hover:hover)]:hover:bg-gray-50 transition-all">
                                 <td class="fi-ta-cell px-6 py-4 font-semibold text-gray-900">{{ $item['product_name'] }}</td>
                                 <td class="fi-ta-cell px-6 py-4">
                                     <x-filament::badge :color="$item['type'] === 'variant' ? 'info' : 'gray'">
                                         {{ $item['label'] }}
                                     </x-filament::badge>
                                 </td>
                                 <td class="fi-ta-cell px-6 py-4 font-mono text-gray-600">{{ $item['sku'] }}</td>
                                 <td class="fi-ta-cell px-6 py-4 text-center">
                                     @if($item['current_stock'] === 0)
                                         <x-filament::badge color="danger">Out of Stock</x-filament::badge>
                                     @elseif($item['current_stock'] <= $item['low_threshold'])
                                         <x-filament::badge color="warning">Low Stock</x-filament::badge>
                                     @else
                                         <x-filament::badge color="success">In Stock</x-filament::badge>
                                     @endif
                                 </td>
                                 <td class="fi-ta-cell px-6 py-4 text-center font-semibold text-gray-900 text-sm">
                                     {{ $item['current_stock'] }}
                                 </td>
                                 <td class="fi-ta-cell px-6 py-4 text-end">
                                     <div class="flex items-center justify-end gap-2">
                                         <x-filament::input.wrapper class="w-20">
                                             <x-filament::input 
                                                 type="number" 
                                                 wire:model="stocks.{{ $key }}.new_stock" 
                                                 min="0"
                                                 class="text-center transition-all focus:ring-2 focus:ring-primary-500"
                                             />
                                         </x-filament::input.wrapper>
                                         <x-filament::button 
                                             wire:click="updateStock('{{ $key }}')" 
                                             wire:loading.attr="disabled"
                                             size="sm"
                                             class="transition-all hover:bg-primary-600 hover:shadow-md"
                                         >
                                             Update
                                         </x-filament::button>
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