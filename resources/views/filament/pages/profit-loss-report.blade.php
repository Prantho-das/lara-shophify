<x-filament-panels::page>
    @if(!empty($reportData))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <x-filament::section>
                <x-slot name="heading">Total Income</x-slot>
                <div class="text-2xl font-bold text-success">{{ number_format($reportData['income']['total'], 2) }} ৳</div>
            </x-filament::section>
            <x-filament::section>
                <x-slot name="heading">Total Expenses</x-slot>
                <div class="text-2xl font-bold text-danger">{{ number_format($reportData['expense']['total'], 2) }} ৳</div>
            </x-filament::section>
            <x-filament::section>
                <x-slot name="heading">Net Profit</x-slot>
                <div class="text-2xl font-bold {{ $reportData['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($reportData['net_profit'], 2) }} ৳
                </div>
            </x-filament::section>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-filament::section>
                <x-slot name="heading">Income Transactions</x-slot>
                @if($reportData['income']['transactions']->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">No income transactions in this period.</p>
                @else
                    <x-filament::table>
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Account</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['income']['transactions'] as $t)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3">{{ $t->date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3">{{ $t->account->name }}</td>
                                        <td class="px-4 py-3">{{ $t->description ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-success">+{{ number_format($t->amount, 2) }} ৳</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-filament::table>
                @endif
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Expense Transactions</x-slot>
                @if($reportData['expense']['transactions']->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">No expense transactions in this period.</p>
                @else
                    <x-filament::table>
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Account</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['expense']['transactions'] as $t)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3">{{ $t->date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3">{{ $t->account->name }}</td>
                                        <td class="px-4 py-3">{{ $t->description ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-danger">-{{ number_format($t->amount, 2) }} ৳</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-filament::table>
                @endif
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
