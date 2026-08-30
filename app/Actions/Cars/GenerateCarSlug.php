<?php

namespace App\Actions\Cars;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;
use Illuminate\Support\Str;

class GenerateCarSlug
{
    public function execute(Car $car): ?string
    {
        if ($car->year === null || $car->make_id === null || $car->car_model_id === null || $car->stock_number === null) {
            return null;
        }

        $make = Make::query()->find($car->make_id);
        $model = CarModel::query()->find($car->car_model_id);

        if ($make === null || $model === null) {
            return null;
        }

        return Str::slug(implode(' ', array_filter([
            $car->year,
            $make->name,
            $model->name,
            $car->trim,
            $car->stock_number,
        ])));
    }
}
