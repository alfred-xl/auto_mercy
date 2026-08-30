<?php

namespace App\Actions\Cars;

use App\Models\Car;
use DomainException;

class EnsureCarReadyForPublication
{
    public function execute(Car $car): void
    {
        $missing = $this->missingRequirements($car);

        if ($missing !== []) {
            throw new DomainException('The car cannot be published until these fields are complete: '.implode(', ', $missing).'.');
        }
    }

    public function isReady(Car $car): bool
    {
        return $this->missingRequirements($car) === [];
    }

    /** @return list<string> */
    public function missingRequirements(Car $car): array
    {
        $required = [
            'stock_number' => 'Stock number',
            'slug' => 'Slug',
            'make_id' => 'Make',
            'car_model_id' => 'Car model',
            'body_type_id' => 'Body type',
            'car_stand_id' => 'Car stand',
            'year' => 'Year',
            'price_amount' => 'Price',
            'mileage' => 'Mileage',
            'mileage_unit' => 'Mileage unit',
            'condition' => 'Condition',
            'transmission' => 'Transmission',
            'fuel_type' => 'Fuel type',
            'exterior_colour' => 'Exterior colour',
            'description' => 'Description',
            'primary_image_id' => 'Primary image',
        ];

        $missing = [];

        foreach ($required as $attribute => $label) {
            if (blank($car->getAttribute($attribute))) {
                $missing[] = $label;
            }
        }

        if ($car->primary_image_id === null) {
            return $missing;
        }

        $primaryImage = $car->images()->whereKey($car->primary_image_id)->first();

        if ($primaryImage === null) {
            $missing[] = 'Primary image belonging to this car';
        } elseif ($primaryImage->processing_status !== 'ready') {
            $missing[] = 'Processed primary image';
        } elseif (blank($primaryImage->alt_text)) {
            $missing[] = 'Primary image alternative text';
        }

        return array_values(array_unique($missing));
    }
}
