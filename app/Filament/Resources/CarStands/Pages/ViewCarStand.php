<?php

namespace App\Filament\Resources\CarStands\Pages;

use App\Filament\Resources\CarStands\CarStandResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCarStand extends ViewRecord
{
    protected static string $resource = CarStandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
