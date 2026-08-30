<?php

namespace App\Filament\Resources\Users\Pages;

use App\Actions\Administrators\UpdateAdministrator;
use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use DomainException;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return app(UpdateAdministrator::class)->execute(
                $record, auth()->user(), $data['name'], $data['email'], UserRole::from($data['role']), (bool) $data['is_active'], $data['password'] ?? null,
            );
        } catch (DomainException $exception) {
            Notification::make()->title('Administrator update blocked')->body($exception->getMessage())->danger()->persistent()->send();
            $this->halt();

            return $record;
        }
    }
}
