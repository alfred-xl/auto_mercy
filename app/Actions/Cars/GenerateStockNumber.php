<?php

namespace App\Actions\Cars;

use App\Models\Car;
use LogicException;

class GenerateStockNumber
{
    public function execute(Car $car): string
    {
        if (! $car->exists || $car->getKey() === null) {
            throw new LogicException('A car must be persisted before its stock number can be generated.');
        }

        return 'AM-'.str_pad((string) $car->getKey(), 4, '0', STR_PAD_LEFT);
    }
}
