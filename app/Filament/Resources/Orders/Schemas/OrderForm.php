<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\ToggleButtons;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                ->label('الزبون')
                ->relationship('customer', 'name')
                ->searchable()
                ->required(),

            Select::make('restaurant_id')
                ->label('المطعم')
                ->relationship('restaurant', 'name')
                ->searchable()
                ->required(),

            Select::make('delivery_id')
                ->label('الدليفري')
                ->relationship('delivery', 'name')
                ->searchable()
                ->nullable(),

            ToggleButtons::make('status')
                ->label('حالة الطلب')
                ->options([
                    'pending'           => 'Pending',
                    'accepted'          => 'Accepted',
                    'cooking'           => 'Cooking',
                    'ready_to_receive'  => 'Ready',
                    'on_the_way'        => 'On The Way',
                    'delivered'         => 'Delivered',
                    'canceled'          => 'Canceled',
                ])
                ->inline()
                ->default('pending'),

            TextInput::make('total_price')
                ->label('السعر الكلي')
                ->numeric()
                ->required(),

            TextInput::make('delivery_price')
                ->label('سعر التوصيل')
                ->numeric()
                ->default(0),

            Select::make('payment_method')
                ->label('طريقة الدفع')
                ->options([
                    'cash'   => 'Cash',
                    'online' => 'Online',
                ])
                ->nullable(),

            Select::make('payment_status')
                ->label('حالة الدفع')
                ->options([
                    'paid'   => 'Paid',
                    'unpaid' => 'Unpaid',
                ])
                ->default('unpaid'),

            TextInput::make('customer_address')
                ->label('عنوان الزبون')
                ->maxLength(255)
                ->nullable(),

            TextInput::make('customer_lat')
                ->label('Customer Lat')
                ->numeric()
                ->nullable(),

            TextInput::make('customer_lng')
                ->label('Customer Lng')
                ->numeric()
                ->nullable(),

          
        ]);

    }
}
