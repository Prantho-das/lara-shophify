<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use App\Models\Product;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Customer & Payment Information')
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->label('Select Customer')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')->required(),
                                        TextInput::make('email')->email()->required()->unique('users', 'email'),
                                        TextInput::make('password')->password()->required()->minLength(8),
                                    ])
                                    ->required()
                                    ->columnSpanFull(),
                                
                                Grid::make(3)
                                    ->schema([
                                        Select::make('status')
                                            ->options([
                                                'pending' => 'Pending',
                                                'processing' => 'Processing',
                                                'shipped' => 'Shipped',
                                                'delivered' => 'Delivered',
                                                'cancelled' => 'Cancelled',
                                                'returned' => 'Returned',
                                            ])
                                            ->required()
                                            ->default('pending'),

                                        Select::make('payment_method')
                                            ->options([
                                                'cod' => 'Cash on Delivery',
                                                'bkash' => 'bKash',
                                                'nagad' => 'Nagad',
                                                'sslcommerz' => 'SSLCommerz',
                                                'card' => 'Credit/Debit Card',
                                            ])
                                            ->required()
                                            ->default('cod'),

                                        Select::make('payment_status')
                                            ->options([
                                                'pending' => 'Pending',
                                                'paid' => 'Paid',
                                                'failed' => 'Failed',
                                            ])
                                            ->required()
                                            ->default('pending'),
                                    ]),
                            ]),

                        Section::make('Order Items')
                            ->schema([
                                 Repeater::make('orderItems')
                                     ->relationship('orderItems')
                                     ->hiddenLabel()
                                     ->schema([
                                         Grid::make(12)->schema([
                                             Select::make('product_id')
                                                 ->relationship('product', 'name')
                                                 ->label('Product')
                                                 ->searchable()
                                                 ->preload()
                                                 ->required()
                                                 ->live(onBlur: true)
                                                 ->afterStateUpdated(function ($state, callable $set) {
                                                     $product = Product::find($state);
                                                     if ($product) {
                                                         if ($product->has_variants) {
                                                             $set('variant_id', null);
                                                             $set('unit_price', null);
                                                             $set('quantity', 1);
                                                             $set('total', 0);
                                                         } else {
                                                             $set('variant_id', null);
                                                             $set('unit_price', $product->base_price);
                                                             $set('quantity', 1);
                                                             $set('total', $product->base_price);
                                                         }
                                                     }
                                                 })
                                                 ->columnSpan(fn ($get) => $get('product_id') && Product::find($get('product_id'))?->has_variants ? 6 : 12),

                                             Select::make('variant_id')
                                                 ->label('Variant')
                                                 ->placeholder('Select Variant')
                                                 ->options(function (callable $get) {
                                                     $productId = $get('product_id');
                                                     if (!$productId) return [];
                                                     $product = Product::find($productId);
                                                     if (!$product || !$product->has_variants) return [];
                                                     return $product->variants()
                                                         ->where('is_active', true)
                                                         ->get()
                                                         ->mapWithKeys(function ($var) {
                                                             $attrs = [];
                                                             if (is_array($var->attribute_values)) {
                                                                 foreach ($var->attribute_values as $key => $val) {
                                                                     $attrs[] = "$key: $val";
                                                                 }
                                                             }
                                                             $lbl = implode(', ', $attrs) ?: "Variant #{$var->id}";
                                                             $lbl .= " (৳{$var->price})";
                                                             return [$var->id => $lbl];
                                                     })
                                                     ->toArray();
                                                 })
                                                 ->visible(function (callable $get) {
                                                     $productId = $get('product_id');
                                                     if (!$productId) return false;
                                                     $product = Product::find($productId);
                                                     return $product ? $product->has_variants : false;
                                                 })
                                                 ->required(function (callable $get) {
                                                     $productId = $get('product_id');
                                                     if (!$productId) return false;
                                                     $product = Product::find($productId);
                                                     return $product ? $product->has_variants : false;
                                                 })
                                                 ->live(onBlur: true)
                                                 ->afterStateUpdated(function ($state, callable $set) {
                                                     if ($state) {
                                                         $variant = \App\Models\ProductVariant::find($state);
                                                         if ($variant) {
                                                             $set('unit_price', $variant->price);
                                                             $set('total', $variant->price);
                                                         }
                                                     }
                                                 })
                                                 ->columnSpan(6),
                                         ]),

                                         Grid::make(4)->schema([
                                             TextInput::make('quantity')
                                                 ->numeric()
                                                 ->required()
                                                 ->default(1)
                                                 ->live(onBlur: true)
                                                 ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                     $price = floatval($get('unit_price') ?? 0);
                                                     $qty = intval($state);
                                                     $set('total', $price * $qty);
                                                 }),

                                             TextInput::make('unit_price')
                                                 ->numeric()
                                                 ->required()
                                                 ->prefix('৳')
                                                 ->live(onBlur: true)
                                                 ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                     $price = floatval($state);
                                                     $qty = intval($get('quantity') ?? 1);
                                                     $set('total', $price * $qty);
                                                 }),

                                             TextInput::make('total')
                                                 ->numeric()
                                                 ->readOnly()
                                                 ->prefix('৳'),
                                                 
                                             TextInput::make('tax')
                                                 ->numeric()
                                                 ->readOnly()
                                                 ->prefix('৳')
                                                 ->default(0),
                                         ]),
                                     ])
                                     ->defaultItems(1)
                                     ->live(onBlur: true)
                                     ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                         $items = $get('orderItems') ?? [];
                                         $total = 0;
                                         $taxTotal = 0;
                                         foreach ($items as $index => $item) {
                                             $productId = $item['product_id'] ?? null;
                                             if ($productId) {
                                                 $product = Product::find($productId);
                                                 $lineTax = ($product->tax_rate / 100) * floatval($item['total'] ?? 0);
                                                 $taxTotal += $lineTax;
                                                 $set("orderItems.{$index}.tax", $lineTax);
                                             }
                                             $total += floatval($item['total'] ?? 0);
                                         }
                                         $shipping = floatval($get('shipping_charge') ?? 0);
                                         $set('tax_amount', $taxTotal);
                                         $set('total_amount', $total + $shipping + $taxTotal);
                                     })
                                     ->addActionLabel('Add Product')
                             ]),
                     ])
                     ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Order Summary')
                            ->schema([
                                TextInput::make('shipping_charge')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('৳')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $items = $get('orderItems') ?? [];
                                        $total = 0;
                                        foreach ($items as $item) {
                                            $total += floatval($item['total'] ?? 0);
                                        }
                                        $shipping = floatval($state);
                                        $taxTotal = floatval($get('tax_amount') ?? 0);
                                        $set('total_amount', $total + $shipping + $taxTotal);
                                    }),
                                TextInput::make('tax_amount')
                                    ->numeric()
                                    ->readOnly()
                                    ->prefix('৳')
                                    ->default(0),
                                TextInput::make('total_amount')
                                    ->numeric()
                                    ->required()
                                    ->readOnly()
                                    ->prefix('৳')
                                    ->default(0),
                            ]),

                        Section::make('Shipping Address')
                            ->schema([
                                Textarea::make('shipping_address')
                                    ->required()
                                    ->rows(4)
                                    ->label('Delivery Address Details'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ]);
    }
}
