<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\Stock;
use App\Models\StockMovement;
use Filament\Actions;
use Filament\Infolists;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([Infolists\Components\Section::make('Purchase Order')->schema([Infolists\Components\TextEntry::make('id')->label('PO #'),                Infolists\Components\TextEntry::make('warehouse.name')->label('Warehouse'),                Infolists\Components\TextEntry::make('supplier_name'),                Infolists\Components\TextEntry::make('status')->badge()->color(fn (string $state): string => match ($state) {
            'draft' => 'gray',                    'ordered' => 'info',                    'received' => 'success',                    'cancelled' => 'danger',
        }),                Infolists\Components\TextEntry::make('total_amount')->money('BDT'),                Infolists\Components\TextEntry::make('notes'),                Infolists\Components\TextEntry::make('created_at')->dateTime('M d, Y H:i')])->columns(3),            Infolists\Components\Section::make('Items')->schema([Infolists\Components\TablesEntry::make('items')->schema([Infolists\Components\TextEntry::make('product.name')->label('Product'),                        Infolists\Components\TextEntry::make('quantity'),                        Infolists\Components\TextEntry::make('unit_cost')->money('BDT'),                        Infolists\Components\TextEntry::make('received_quantity')->label('Received')])])->collapsible()]);
    }

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()->visible(fn ($record) => $record->status === 'draft'),            Actions\Action::make('receive')->label('Receive Items')->icon('heroicon-o-check-circle')->color('success')->visible(fn ($record) => in_array($record->status, ['draft', 'ordered']))->requiresConfirmation()->modalHeading('Receive Purchase Order')->modalDescription('This will add all ordered items to stock. Continue?')->action(function ($record) {
            foreach ($record->items as $item) {
                $receivedQty = $item->received_quantity ?: $item->quantity;
                $stock = Stock::firstOrCreate(['product_id' => $item->product_id,                                'variant_id' => $item->variant_id,                                'warehouse_id' => $record->warehouse_id], ['quantity' => 0]);
                $stock->increment('quantity', $receivedQty);
                StockMovement::create(['stock_id' => $stock->id,                            'type' => 'in',                            'quantity' => $receivedQty,                            'to_warehouse_id' => $record->warehouse_id,                            'reference_type' => PurchaseOrder::class,                            'reference_id' => $record->id,                            'notes' => "PO #{$record->id} received",                            'created_by' => auth()->id()]);
                $item->update(['received_quantity' => $receivedQty]);
            }                    $total = $record->items->sum(fn ($item) => $item->unit_cost * ($item->received_quantity ?: $item->quantity));
            $record->update(['status' => 'received', 'total_amount' => $total]);
            Notification::make()->title('Purchase Order Received')->body("Stock updated for {$record->warehouse->name}.")->success()->send();
        })];
    }
}
