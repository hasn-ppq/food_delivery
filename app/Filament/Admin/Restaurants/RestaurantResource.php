<?php
namespace App\Filament\Admin\Restaurants;

use App\Filament\Admin\Restaurants\Pages\CreateRestaurant;
use App\Filament\Admin\Restaurants\Pages\EditRestaurant;
use App\Filament\Admin\Restaurants\Pages\ListRestaurants;
use App\Filament\Admin\Restaurants\Schemas\RestaurantForm;
use App\Filament\Admin\Restaurants\Tables\RestaurantsTable;
use App\Filament\Admin\Restaurants\RelationManagers\MealsRelationManager;
use App\Filament\Admin\Restaurants\RelationManagers\OrdersRelationManager;
use App\Models\Restaurant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingStorefront;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RestaurantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestaurantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
           MealsRelationManager::class,
           OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurants::route('/'),
            'create' => CreateRestaurant::route('/create'),
            'edit' => EditRestaurant::route('/{record}/edit'),
        ];
    }
}
