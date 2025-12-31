<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Role;
use Filament\Forms\Components\Select;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               TextInput::make('name')->required(),

TextInput::make('phone')
    ->required()
    ->unique(ignoreRecord: true),
    TextInput::make('email')
    ->email()
    ->required()
    ->unique(ignoreRecord: true), 


TextInput::make('password')
    ->password()
    ->required(fn ($record) => !$record)
    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),

Select::make('role_id')
    ->relationship('role', 'name')
    ->options(
        Role::whereIn('slug', ['owner', 'delivery'])
            ->pluck('name', 'id')
    )
    ->required(),

            ]);
    }
}
