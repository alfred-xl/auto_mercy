<?php

namespace App\Filament\Resources\Cars\Pages;

use App\Filament\Resources\Cars\Actions\CarLifecycleActions;
use App\Filament\Resources\Cars\CarResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCar extends EditRecord
{
    protected static string $resource = CarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            ...CarLifecycleActions::make(),
            DeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill($data);
        $record->forceFill(['updated_by' => auth()->id()])->save();

        return $record;
    }
}
