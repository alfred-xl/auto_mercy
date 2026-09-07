<?php

namespace App\Actions\Cars;

use App\Models\Car;
use Illuminate\Support\Str;

class GenerateCarSlug
{
    public function execute(Car $car): ?string
    {
        if ($car->year === null || blank($car->make) || blank($car->model) || $car->stock_number === null) {
            return null;
        }

        return Str::slug(implode(' ', array_filter([
            $car->year,
            $car->make,
            $car->model,
            $car->trim,
            $car->stock_number,
        ])));
    }
}
