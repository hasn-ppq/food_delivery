<?php

namespace App\Filament\Restaurant\Resources\Meals;

use App\Filament\Restaurant\Resources\Meals\Pages\CreateMeal;
use App\Filament\Restaurant\Resources\Meals\Pages\EditMeal;
use App\Filament\Restaurant\Resources\Meals\Pages\ListMeals;
use App\Filament\Restaurant\Resources\Meals\Schemas\MealForm;
use App\Filament\Restaurant\Resources\Meals\Tables\MealsTable;
use App\Models\Meal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use App\Models\Restaurant;
use App\Models\User;



class MealResource extends Resource
{
    protected static ?string $model = Meal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'meals';

    public static function form(Schema $schema): Schema
    {
        return MealForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MealsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

public static function getEloquentQuery(): Builder
{
    $user = Auth::user();
    $restaurant = $user->restaurants;

    if (!$restaurant) {
        // إشعار
        Notification::make()
            ->title('يرجى إضافة مطعم أولاً')
            ->warning()
            ->send();

        // منع عرض أي بيانات
        return parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    return parent::getEloquentQuery()
        ->where('restaurant_id', $restaurant->id);
}


    public static function getPages(): array
    {
        return [
            'index' => ListMeals::route('/'),
            'create' => CreateMeal::route('/create'),
            'edit' => EditMeal::route('/{record}/edit'),
        ];
    }
}
