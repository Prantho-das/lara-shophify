<?php

namespace App\Filament\Resources\EmployeeLoanResource\Pages;

use App\Filament\Resources\EmployeeLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeLoan extends CreateRecord
{
    protected static string $resource = EmployeeLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['remaining_amount'])) {
            $data['remaining_amount'] = $data['amount'];
        }

return $data;
    }
}
