<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_to_steadfast')
                ->label('Send to Steadfast')
                ->icon('heroicon-o-truck')
                ->color('success')
                ->modalHeading('Send Order to Steadfast Courier')
                ->modalSubmitActionLabel('Book Delivery')
                ->form([
                    \Filament\Forms\Components\TextInput::make('cod_amount')
                        ->label('COD Amount')
                        ->numeric()
                        ->required()
                        ->default(fn ($record) => $record->payment_status === 'paid' ? 0 : $record->total_amount),
                    \Filament\Forms\Components\Textarea::make('note')
                        ->label('Special Note/Instruction')
                        ->placeholder('Optional delivery instructions...'),
                ])
                ->action(function ($record, array $data) {
                    $result = \App\Services\CourierService::sendToSteadfast($record, $data['cod_amount'], $data['note']);
                    if ($result['success']) {
                        $record->update([
                            'courier_name' => 'Steadfast',
                            'courier_tracking_code' => $result['tracking_code'],
                            'courier_status' => $result['status'],
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Sent to Steadfast Successfully!')
                            ->body('Tracking ID: ' . $result['tracking_code'])
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Steadfast Error')
                            ->body($result['message'])
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn ($record) => empty($record->courier_tracking_code)),

            Action::make('send_to_pathao')
                ->label('Send to Pathao')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->modalHeading('Send Order to Pathao Courier')
                ->modalSubmitActionLabel('Book Delivery')
                ->form([
                    \Filament\Forms\Components\TextInput::make('cod_amount')
                        ->label('COD Amount')
                        ->numeric()
                        ->required()
                        ->default(fn ($record) => $record->payment_status === 'paid' ? 0 : $record->total_amount),
                    \Filament\Forms\Components\Textarea::make('note')
                        ->label('Special Note/Instruction')
                        ->placeholder('Optional delivery instructions...'),
                ])
                ->action(function ($record, array $data) {
                    $result = \App\Services\CourierService::sendToPathao($record, $data['cod_amount'], $data['note']);
                    if ($result['success']) {
                        $record->update([
                            'courier_name' => 'Pathao',
                            'courier_tracking_code' => $result['tracking_code'],
                            'courier_status' => $result['status'],
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Sent to Pathao Successfully!')
                            ->body('Tracking ID: ' . $result['tracking_code'])
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Pathao Error')
                            ->body($result['message'])
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn ($record) => empty($record->courier_tracking_code)),

            DeleteAction::make(),
        ];
    }
}
