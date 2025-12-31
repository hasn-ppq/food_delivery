<?php

namespace App\Filament\Restaurant\Resources\Meals\Pages;

use App\Filament\Restaurant\Resources\Meals\MealResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMeal extends EditRecord
{
    protected static string $resource = MealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
