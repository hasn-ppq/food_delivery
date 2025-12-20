<?php

namespace App\Filament\Driver\Resources\Orders\Pages;

use App\Filament\Driver\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
