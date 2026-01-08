<?php

namespace App\Filament\Driver\Resources\Orders\Pages;

use App\Filament\Driver\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\EditRecord;


class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
           
          Action::make('back')
            ->label('رجوع')
            ->url($this->getResource()::getUrl('index')),
        ];
    }
    protected function getformActions(): array
    {
        return [

          Action::make('accept')
    ->label(fn (Order $record) => 
        $record->status === 'on_the_way'
            ? 'تم الاستلام'
            : 'استلام الطلب'
    )
    ->color('success')
    ->requiresConfirmation()
    ->action(function (Order $record) {
        $record->update([
            'delivery_id' => Auth::id(),
            'status' => 'on_the_way',
            'delivery_assigned_at' => now(),
        ]);
    }),

            
        ];
    }
}
