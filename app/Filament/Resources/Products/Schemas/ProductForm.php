<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Product Details')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('barcode')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\RichEditor::make('description')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        \Filament\Schemas\Components\Section::make('Images')
                            ->schema([
                                \Filament\Forms\Components\Repeater::make('images')
                                    ->relationship('images')
                                    ->schema([
                                        \Filament\Forms\Components\FileUpload::make('path')
                                            ->image() ->disk('public')
                                            ->required()
                                            ->directory('products')
                                            ->columnSpan(1),
                                        \Filament\Forms\Components\Toggle::make('is_primary')
                                            ->label('Featured Image')
                                            ->default(false)
                                            ->columnSpan(1),
                                        \Filament\Forms\Components\TextInput::make('sort_order')
                                            ->numeric()
                                            ->default(0)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->orderColumn('sort_order'),
                            ]),

                        \Filament\Schemas\Components\Section::make('Pricing')
                            ->schema([
                                TextInput::make('base_price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('৳')
                                    ->required(),
                                TextInput::make('compare_price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('৳'),
                                TextInput::make('tax_rate')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->suffix('%'),
                                TextInput::make('stock_quantity')
                                    ->label('Stock Quantity')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(100)
                                    ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => !$get('has_variants'))
                                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => !$get('has_variants')),
                            ])->columns(2),

                        \Filament\Schemas\Components\Section::make('Variants')
                            ->schema([
                                Toggle::make('has_variants')
                                    ->label('This product has options, like size or color')
                                    ->live(),

                                \Filament\Forms\Components\KeyValue::make('attributes')
                                    ->label('Options')
                                    ->keyLabel('Option name (e.g. Size)')
                                    ->valueLabel('Option values (comma-separated, e.g. S, M, L)')
                                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('has_variants'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (\Filament\Schemas\Components\Utilities\Set $set, \Filament\Schemas\Components\Utilities\Get $get, $state) {
                                        $attributes = $state ?? [];
                                        
                                        $arraysToCombine = [];
                                        foreach ($attributes as $name => $values) {
                                            $valuesArray = array_filter(array_map('trim', explode(',', $values)));
                                            if (!empty($valuesArray)) {
                                                $arraysToCombine[$name] = $valuesArray;
                                            }
                                        }

                                        if (empty($arraysToCombine)) {
                                            $set('variants', []);
                                            return;
                                        }

                                        $combinations = [[]];
                                        foreach ($arraysToCombine as $attributeName => $values) {
                                            $temp = [];
                                            foreach ($combinations as $combination) {
                                                foreach ($values as $value) {
                                                    $temp[] = array_merge($combination, [$attributeName => $value]);
                                                }
                                            }
                                            $combinations = $temp;
                                        }

                                        $existingVariants = $get('variants') ?? [];
                                        $existingMapped = [];
                                        foreach ($existingVariants as $variant) {
                                            if (isset($variant['variant_name'])) {
                                                $existingMapped[$variant['variant_name']] = $variant;
                                            }
                                        }

                                        $variants = [];
                                        $baseSku = $get('slug') ? strtoupper(substr($get('slug'), 0, 4)) : 'PROD';
                                        foreach ($combinations as $index => $combo) {
                                            $skuStub = implode('-', array_values($combo));
                                            $variantName = implode(' / ', array_values($combo));
                                            
                                            if (isset($existingMapped[$variantName])) {
                                                $variants[(string) \Illuminate\Support\Str::uuid()] = $existingMapped[$variantName];
                                            } else {
                                                $variants[(string) \Illuminate\Support\Str::uuid()] = [
                                                    'attribute_values' => $combo,
                                                    'variant_name' => $variantName,
                                                    'sku' => $baseSku . '-' . strtoupper($skuStub) . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                                                    'price' => $get('base_price') ?? 0,
                                                    'stock_quantity' => 0,
                                                    'is_active' => true,
                                                ];
                                            }
                                        }
                                        $set('variants', $variants);
                                    }),

                                \Filament\Forms\Components\Repeater::make('variants')
                                    ->relationship('variants')
                                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('has_variants'))
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->schema([
                                        \Filament\Forms\Components\Hidden::make('attribute_values'),
                                        \Filament\Forms\Components\TextInput::make('variant_name')
                                            ->label('Variant')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->columnSpan(2),
                                        TextInput::make('sku')
                                            ->required()
                                            ->maxLength(255)
                                            ->distinct()
                                            ->columnSpan(1),
                                        TextInput::make('barcode')
                                            ->maxLength(255)
                                            ->columnSpan(1),
                                        TextInput::make('price')
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('৳')
                                            ->required()
                                            ->columnSpan(1),
                                        TextInput::make('stock_quantity')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->required()
                                            ->columnSpan(1),
                                    ])
                                    ->columns(5)
                                    ->columnSpanFull()
                                    ->defaultItems(0),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        \Filament\Schemas\Components\Section::make('Status')
                            ->schema([
                                \Filament\Forms\Components\Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'draft' => 'Draft',
                                        'archived' => 'Archived',
                                    ])
                                    ->required()
                                    ->in(['active', 'draft', 'archived'])
                                    ->default('draft'),
                                \Filament\Forms\Components\Toggle::make('featured')
                                    ->label('Show on Homepage')
                                    ->default(false),
                            ]),
                            
                        \Filament\Schemas\Components\Section::make('Organization')
                            ->schema([
                                \Filament\Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                \Filament\Forms\Components\Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload(),
                                \Filament\Forms\Components\TagsInput::make('tags'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ]);
    }
}
