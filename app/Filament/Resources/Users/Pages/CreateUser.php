<?php

namespace App\Filament\Resources\Users\Pages;

use App\Actions\Administrators\CreateAdministrator;
use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateAdministrator::class)->execute(
            auth()->user(), $data['name'], $data['email'], UserRole::from($data['role']), (bool) $data['is_active'], $data['password'],
        );
    }
}
