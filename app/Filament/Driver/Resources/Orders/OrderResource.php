<?php

namespace App\Filament\Driver\Resources\Orders;

use App\Filament\Driver\Resources\Orders\Pages\CreateOrder;
use App\Filament\Driver\Resources\Orders\Pages\EditOrder;
use App\Filament\Driver\Resources\Orders\Pages\ListOrders;
use App\Filament\Driver\Resources\Orders\Schemas\OrderForm;
use App\Filament\Driver\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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

     public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', ['preparing', 'on_the_way'])
            ->where(function ($query) {
                $query->whereNull('driver_id')
                      ->orWhere('driver_id', auth::id());
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            
        ];
    }
}