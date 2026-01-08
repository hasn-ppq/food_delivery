<?php

namespace App\Filament\Driver\Resources\Orders;

use App\Filament\Driver\Resources\Orders\Pages\CreateOrder;
use App\Filament\Driver\Resources\Orders\Pages\EditOrder;
use App\Filament\Driver\Resources\Orders\Pages\ListOrders;
use App\Filament\Driver\Resources\Orders\Pages\ViewOrder;
use App\Filament\Driver\Resources\Orders\Schemas\OrderForm;
use App\Filament\Driver\Resources\Orders\Schemas\OrderInfolist;
use App\Filament\Driver\Resources\Orders\Tables\OrdersTable;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
  use Illuminate\Database\Eloquent\Builder;
 use App\Filament\Driver\Resources\Orders\RelationManagers\ItemsRelationManager;



class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $slug = 'ready-orders';
    protected static ?string $navigationLabel = 'الطلبات الجاهزة';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            itemsRelationManager::class,
        ];
    }
public static function getEloquentQuery(): Builder
{
    $driverLat = Auth::user()->lat;
    $driverLng = Auth::user()->lng;

    $query = Order::select('*')
        ->selectRaw("
            (6371 * acos(
                cos(radians(?)) *
                cos(radians(customer_lat)) *
                cos(radians(customer_lng) - radians(?)) +
                sin(radians(?)) *
                sin(radians(customer_lat))
            )) AS distance
        ", [$driverLat, $driverLng, $driverLat])
        ->having('distance', '<=', 3)
        ->whereNull('delivery_id')
        ->where('status', 'ready_to_receive');

    return $query;
}


    public static function getPages(): array
    {
        return [
           'index' => ListOrders::route('/'),
           'edit'=> EditOrder::route(' /{record}/edit'),
    

        
           
        ];
    }
}
