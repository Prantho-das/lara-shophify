<?php

namespace App\Filament\Resources\LocationRates\Pages;

use App\Filament\Resources\LocationRates\LocationRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLocationRates extends ManageRecords
{
    protected static string $resource = LocationRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
