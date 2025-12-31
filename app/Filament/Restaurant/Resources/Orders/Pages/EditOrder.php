<?php

namespace App\Filament\Restaurant\Resources\Orders\Pages;

use App\Filament\Restaurant\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;
    protected function getFormActions(): array
{
    return [
        Action::make('back')
            ->label('رجوع')
            ->url($this->getResource()::getUrl('index')),
    ];
}


}
