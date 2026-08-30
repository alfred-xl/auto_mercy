<?php

namespace App\Filament\Resources\Makes\Pages;

use App\Filament\Resources\Makes\MakeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMake extends EditRecord
{
    protected static string $resource = MakeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()->visible(fn ($record): bool => $record->cars()->doesntExist()),
        ];
    }
}
