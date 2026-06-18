<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Guest Customer'),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->formatStateUsing(fn ($state) => (\App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '৳') . ' ' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->searchable(),
                TextColumn::make('shipping_charge')
                    ->formatStateUsing(fn ($state) => (\App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '৳') . ' ' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('courier_name')
                    ->label('Courier')
                    ->placeholder('N/A'),
                TextColumn::make('courier_tracking_code')
                    ->label('Tracking Code')
                    ->searchable()
                    ->placeholder('Not Shipped'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
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
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
