<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clipboard;

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
            //
        ];
    }
     public static function getEloquentQuery(): Builder
    {
        // حتى يظهر فقط الطلبات التابعة للمطعم الحالي
        $restaurantId = auth::user()->restaurant->id ?? null;
        return parent::getEloquentQuery()->where('restaurant_id', $restaurantId);
    }
    

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            
        ];
    }
}
