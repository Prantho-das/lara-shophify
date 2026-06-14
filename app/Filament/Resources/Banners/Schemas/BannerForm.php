<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Homepage Slider / Banner Details')
                    ->description('Upload images and add text for the homepage banners.')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->required()
                            ->directory('banners')
                            ->imageEditor()
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->label('Banner Text (Optional)')
                            ->placeholder('e.g. Summer Sale 50% Off')
                            ->columnSpan(1),

                        TextInput::make('link')
                            ->label('Button Link (Optional)')
                            ->placeholder('e.g. /category/summer')
                            ->columnSpan(1),

                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->required()
                            ->default('active')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
