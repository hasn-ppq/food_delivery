<?php

namespace App\Filament\Restaurant\Resources\Meals\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;

class MealForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            Hidden::make('restaurant_id')
                ->default(fn () => Auth::user()->restaurants->id)
                ->required(),

            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('price')
                ->numeric()
                ->required(),

            TextInput::make('discount_price')
                ->numeric(),
           Select::make('status')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->default('active'),
            
                 Textarea::make('description')
                ->rows(3),
             Toggle::make('is_featured')
                ->onColor('success')
                ->label('Featured'),
           
            FileUpload::make('image')
                ->image()
                ->directory('meals'),
                
            ]);
    }
}
