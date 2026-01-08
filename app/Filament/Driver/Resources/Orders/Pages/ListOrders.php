<?php

namespace App\Filament\Driver\Resources\Orders\Pages;

use App\Filament\Driver\Resources\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

   
}
