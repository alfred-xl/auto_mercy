<?php

namespace App\Actions\Cars;

use App\Models\Car;
use App\Models\CarModel;
use DomainException;

class EnsureCarModelMatchesMake
{
    public function execute(Car $car): void
    {
        if ($car->make_id === null || $car->car_model_id === null) {
            return;
        }

        $matches = CarModel::query()
            ->whereKey($car->car_model_id)
            ->where('make_id', $car->make_id)
            ->exists();

        if (! $matches) {
            throw new DomainException('The selected vehicle model does not belong to the selected make.');
        }
    }
}
