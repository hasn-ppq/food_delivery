<?php

namespace App\Filament\Admin\Users\Pages;

use App\Filament\Admin\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
