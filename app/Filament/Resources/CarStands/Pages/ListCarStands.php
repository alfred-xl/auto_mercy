<?php

namespace App\Filament\Resources\CarStands\Pages;

use App\Filament\Resources\CarStands\CarStandResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCarStands extends ListRecords
{
    protected static string $resource = CarStandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
