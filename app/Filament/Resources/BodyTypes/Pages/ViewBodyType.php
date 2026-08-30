<?php

namespace App\Filament\Resources\BodyTypes\Pages;

use App\Filament\Resources\BodyTypes\BodyTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBodyType extends ViewRecord
{
    protected static string $resource = BodyTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
