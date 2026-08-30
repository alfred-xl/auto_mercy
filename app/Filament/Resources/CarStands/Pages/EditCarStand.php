<?php

namespace App\Filament\Resources\CarStands\Pages;

use App\Filament\Resources\CarStands\CarStandResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCarStand extends EditRecord
{
    protected static string $resource = CarStandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()->visible(fn ($record): bool => $record->cars()->doesntExist()),
        ];
    }
}
