<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Models\EmployeeLoan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['net_salary'] = $this->calculateNetSalary($data);

        if (empty($data['loan_deduction']) || $data['loan_deduction'] == 0) {
            $totalLoanDeduction = EmployeeLoan::where('employee_id', $data['employee_id'])
                ->where('status', 'active')
                ->sum('monthly_deduction');
            $data['loan_deduction'] = $totalLoanDeduction;
            $data['net_salary'] = $this->calculateNetSalary($data);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $employeeId = $this->record->employee_id;
        $loans = EmployeeLoan::where('employee_id', $employeeId)
            ->where('status', 'active')
            ->get();

        foreach ($loans as $loan) {
            $loan->deduct($loan->monthly_deduction);
        }
    }

    private function calculateNetSalary(array $data): float
    {
        $basic = (float) ($data['basic_salary'] ?? 0);
        $allowances = collect($data['allowances'] ?? [])->sum('amount');
        $deductions = collect($data['deductions'] ?? [])->sum('amount');
        $loan = (float) ($data['loan_deduction'] ?? 0);
        $tax = (float) ($data['tax_deduction'] ?? 0);

        return max(0, $basic + $allowances - $deductions - $loan - $tax);
    }
}
