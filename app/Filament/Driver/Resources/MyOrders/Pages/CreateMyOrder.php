<?php

namespace App\Filament\Driver\Resources\MyOrders\Pages;

use App\Filament\Driver\Resources\MyOrders\MyOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyOrder extends CreateRecord
{
    protected static string $resource = MyOrderResource::class;
}
