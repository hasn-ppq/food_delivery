<?php

namespace App\Filament\Admin\Restaurants\Pages;

use App\Filament\Admin\Restaurants\RestaurantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurant extends CreateRecord
{
    protected static string $resource = RestaurantResource::class;
}
