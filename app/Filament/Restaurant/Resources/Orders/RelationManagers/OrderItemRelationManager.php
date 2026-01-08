<?php

namespace App\Filament\Restaurant\Resources\Orders\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderItemRelationManager extends RelationManager
{
    protected static string $relationship = 'Items';

    public function form(Schema $schema): Schema
    {
        
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([

                     TextColumn::make('meal_name')
                    ->label('Meal'),

                TextColumn::make('quantity')
                    ->sortable(),

                TextColumn::make('price')
                    ->money('IQD'),

                TextColumn::make('total')
                    ->money('IQD'),
            ]) ;
           
        
            
            
    }
}
