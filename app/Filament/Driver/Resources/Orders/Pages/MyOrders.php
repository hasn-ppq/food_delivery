<?php

namespace App\Filament\Driver\Resources\Orders\Pages;

use App\Filament\Driver\Resources\Orders\OrderResource;
use Filament\Resources\Pages\Page;

class MyOrders extends Page
{
    protected static string $resource = OrderResource::class;

    protected string $view = 'filament.driver.resources.orders.pages.my-orders';
}
