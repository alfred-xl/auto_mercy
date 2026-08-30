<?php

namespace App\Filament\Resources\Makes\Pages;

use App\Filament\Resources\Makes\MakeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMake extends ViewRecord
{
    protected static string $resource = MakeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
