<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(3)
                    ->schema([
                        // Main Content Column
                        \Filament\Schemas\Components\Grid::make(1)
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Page Content')
                                    ->description('Configure the main text body and title of your static page.')
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->placeholder('e.g. Terms of Service')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                                        TextInput::make('slug')
                                            ->required()
                                            ->unique(\App\Models\Page::class, 'slug', ignoreRecord: true)
                                            ->placeholder('e.g. terms-of-service'),
                                        RichEditor::make('content')
                                            ->label('Content / Body Text')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                            ])
                            ->columnSpan(2),

                        // Sidebar Options Column
                        \Filament\Schemas\Components\Grid::make(1)
                            ->schema([
                                \Filament\Schemas\Components\Section::make('Status & Visibility')
                                    ->schema([
                                        Select::make('status')
                                            ->options([
                                                'active' => 'Active / Published',
                                                'draft' => 'Draft / Hidden',
                                            ])
                                            ->default('active')
                                            ->required(),
                                    ]),

                                \Filament\Schemas\Components\Section::make('Search Engine Optimization (SEO)')
                                    ->description('Meta settings for search engines.')
                                    ->schema([
                                        TextInput::make('seo_title')
                                            ->label('Meta Title')
                                            ->placeholder('SEO optimized title'),
                                        Textarea::make('seo_description')
                                            ->label('Meta Description')
                                            ->rows(3)
                                            ->placeholder('Compelling description for search listings...'),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
