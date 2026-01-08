<?php

namespace App\Filament\Driver\Resources\MyOrders;

use App\Filament\Driver\Resources\MyOrders\Pages\CreateMyOrder;
use App\Filament\Driver\Resources\MyOrders\Pages\EditMyOrder;
use App\Filament\Driver\Resources\MyOrders\Pages\ListMyOrders;
use App\Filament\Driver\Resources\MyOrders\Schemas\MyOrderForm;
use App\Filament\Driver\Resources\MyOrders\Tables\MyOrdersTable;
use App\Filament\Driver\Resources\Orders\RelationManagers\ItemsRelationManager;
use App\Models\MyOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class MyOrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $slug = 'my-orders';   

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'طلباتي المقبولة';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MyOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MyOrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }
    public static function getEloquentQuery(): Builder
{
    return Order::query()->where('delivery_id', Auth::id())
            ->orderBy('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMyOrders::route('/'),
            'edit' => EditMyOrder::route('/{record}/edit'),
        ];
    }
}
