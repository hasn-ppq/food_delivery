<?php

namespace App\Filament\Restaurant\Resources\Restaurants\Pages;

use App\Filament\Restaurant\Resources\Restaurants\RestaurantResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRestaurant extends CreateRecord
{
    protected static string $resource = RestaurantResource::class;
   protected function authorizeAccess(): void
{
    abort_if(Auth::user()->restaurants !== null, 403);
}

}
