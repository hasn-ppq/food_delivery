<?php

namespace App\Filament\Admin\Restaurants\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MealsRelationManager extends RelationManager
{
    protected static string $relationship = 'meals';
     protected static ?string $title = 'الوجبات';

  
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                 ImageColumn::make('image')
                    ->label('صورة'),

                 TextColumn::make('name')
                    ->label('اسم الوجبة')
                    ->searchable()
                    ->sortable(),

                 TextColumn::make('price')
                    ->label('السعر')
                    ->sortable(),

                 TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            
            
            ;
    }
}
