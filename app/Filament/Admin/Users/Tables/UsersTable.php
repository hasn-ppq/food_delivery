<?php

namespace App\Filament\Admin\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use App\Notifications\AccountApproved;
use App\Notifications\AccountRejected;
use Filament\Tables\Columns\ToggleColumn;

class UsersTable
{
    public static function configure(Table $table): Table
    { 
        return $table
         ->poll('15s')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('role.name')->label('Role'),
                IconColumn::make('is_active')
                ->boolean()
                ->label('مفعل'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                  Action::make('approve')
                ->label('موافقة')
                ->color('success')
                ->visible(fn ($record) => ! $record->is_active)
                ->requiresConfirmation()
                ->action(function ($record) {
                    $record->update(['is_active' => true]);
                     $record->notify(new AccountApproved());
                    
                }),

            Action::make('reject')
                ->label('رفض')
                ->color('danger')
                ->visible(fn ($record) => ! $record->is_active)
                ->requiresConfirmation()
                ->action(function ($record) {
                   $record->notify(new AccountRejected());
                    $record->delete();
                }),
                Action::make('deactivate')
                 ->label('إلغاء التفعيل')
                 ->icon('heroicon-o-x-circle')
                 ->color('danger')
                 ->visible(fn ($record) => $record->is_active)
                 ->requiresConfirmation()
                 ->action(function ($record) {
                  $record->update([
                  'is_active' => false,
        ]);
    }),
            ])
            ;
    }
}
