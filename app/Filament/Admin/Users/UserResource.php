<?php

namespace App\Filament\Admin\Users;

use App\Filament\Admin\Users\Pages\CreateUser;
use App\Filament\Admin\Users\Pages\EditUser;
use App\Filament\Admin\Users\Pages\ListUsers;
use App\Filament\Admin\Users\Schemas\UserForm;
use App\Filament\Admin\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->whereHas('role', function ($q) {
            $q->whereIn('slug', [
                'owner',
                'delivery',
            ]);
        });
}


    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            
        ];
    }
}
