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
            throw new DomainException('The car cannot be made available until these fields are complete: '.implode(', ', $missing).'.');
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
            'listing_category' => 'Listing category',
            'make' => 'Make',
            'model' => 'Model',
            'year' => 'Year',
            'price_amount' => 'Price',
            'description' => 'Description',
        ];
        $missing = [];

        foreach ($required as $attribute => $label) {
            if (blank($car->getAttribute($attribute))) {
                $missing[] = $label;
            }
        }

        if (! $car->images()->where('processing_status', 'ready')->exists()) {
            $missing[] = 'At least one ready image';
        }

        return $missing;
    }
}
