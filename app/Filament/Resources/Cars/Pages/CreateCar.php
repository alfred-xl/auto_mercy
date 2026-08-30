<?php

namespace App\Filament\Resources\Cars\Pages;

use App\Filament\Resources\Cars\CarResource;
use App\Models\Car;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCar extends CreateRecord
{
    protected static string $resource = CarResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $car = new Car;
        $car->fill($data);
        $car->forceFill([
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ])->save();

        return $car;
    }

    protected function getRedirectUrl(): string
    {
        return CarResource::getUrl('edit', ['record' => $this->record]);
    }
}
