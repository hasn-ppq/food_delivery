<?php

namespace App\Filament\Restaurant\Resources\Meals\Pages;

use App\Filament\Restaurant\Resources\Meals\MealResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeal extends CreateRecord
{
    protected static string $resource = MealResource::class;
}
