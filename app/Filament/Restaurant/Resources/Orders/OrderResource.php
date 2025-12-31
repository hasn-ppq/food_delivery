<?php

namespace App\Filament\Restaurant\Resources\Orders;

use App\Filament\Restaurant\Resources\Orders\Pages\CreateOrder;
use App\Filament\Restaurant\Resources\Orders\Pages\EditOrder;
use App\Filament\Restaurant\Resources\Orders\Pages\ListOrders;
use App\Filament\Restaurant\Resources\Orders\Schemas\OrderForm;
use App\Filament\Restaurant\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use App\Filament\Restaurant\Resources\Orders\RelationManagers\OrderItemRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

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
        OrderItemRelationManager::class,
             
        ];
    }
    
   

    

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            //'create' => CreateOrder::route('/create'),
           'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
