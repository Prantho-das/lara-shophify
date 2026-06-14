<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Notifications\Notification;

class InventoryManager extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'Inventory Stock Manager';

    protected string $view = 'filament.pages.inventory-manager';

    // Model properties for binding edits
    public $stocks = [];

    public function mount()
    {
        $this->loadStocks();
    }

    public function loadStocks()
    {
        $products = Product::with('variants')->get();
        $this->stocks = [];

        foreach ($products as $product) {
            if ($product->has_variants) {
                foreach ($product->variants as $variant) {
                    $attrs = is_array($variant->attribute_values) ? $variant->attribute_values : json_decode($variant->attribute_values, true);
                    $varLabel = implode(' / ', $attribs ?? $attrs);
                    
                    $this->stocks['variant-' . $variant->id] = [
                        'id' => $variant->id,
                        'type' => 'variant',
                        'product_name' => $product->name,
                        'label' => $varLabel ?: "Variant #{$variant->id}",
                        'sku' => $variant->sku,
                        'current_stock' => $variant->stock_quantity,
                        'new_stock' => $variant->stock_quantity,
                        'low_threshold' => 5,
                    ];
                }
            } else {
                $this->stocks['product-' . $product->id] = [
                    'id' => $product->id,
                    'type' => 'product',
                    'product_name' => $product->name,
                    'label' => 'Standard Item (No Options)',
                    'sku' => $product->barcode ?: 'N/A',
                    'current_stock' => $product->stock_quantity,
                    'new_stock' => $product->stock_quantity,
                    'low_threshold' => 10,
                ];
            }
        }
    }

    public function updateStock($key)
    {
        if (!isset($this->stocks[$key])) {
            return;
        }

        $item = $this->stocks[$key];
        $newQty = intval($item['new_stock']);

        if ($newQty < 0) {
            Notification::make()
                ->title('Invalid Quantity')
                ->body('Stock quantity cannot be less than 0.')
                ->danger()
                ->send();
            return;
        }

        if ($item['type'] === 'variant') {
            $variant = ProductVariant::find($item['id']);
            if ($variant) {
                $variant->update(['stock_quantity' => $newQty]);
                $this->stocks[$key]['current_stock'] = $newQty;
                Notification::make()
                    ->title('Stock Updated Successfully')
                    ->body("{$item['product_name']} ({$item['label']}) stock set to {$newQty}")
                    ->success()
                    ->send();
            }
        } else {
            $product = Product::find($item['id']);
            if ($product) {
                $product->update(['stock_quantity' => $newQty]);
                $this->stocks[$key]['current_stock'] = $newQty;
                Notification::make()
                    ->title('Stock Updated Successfully')
                    ->body("{$item['product_name']} stock set to {$newQty}")
                    ->success()
                    ->send();
            }
        }
    }
}
