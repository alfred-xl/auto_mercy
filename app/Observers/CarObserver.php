<?php

namespace App\Observers;

use App\Actions\Cars\EnsureCarModelMatchesMake;
use App\Actions\Cars\GenerateCarSlug;
use App\Actions\Cars\GenerateStockNumber;
use App\Enums\CarStatus;
use App\Models\Car;
use LogicException;

class CarObserver
{
    public function __construct(
        private readonly EnsureCarModelMatchesMake $ensureCarModelMatchesMake,
        private readonly GenerateStockNumber $generateStockNumber,
        private readonly GenerateCarSlug $generateCarSlug,
    ) {}

    public function saving(Car $car): void
    {
        $this->ensureCarModelMatchesMake->execute($car);
    }

    public function creating(Car $car): void
    {
        if ($car->status !== null && $car->status !== CarStatus::Draft) {
            throw new LogicException('Cars must be created in Draft status.');
        }
    }

    public function updating(Car $car): void
    {
        if ($car->isDirty('status') && ! $car->statusTransitionInProgress) {
            throw new LogicException('Car status changes must use the status transition action.');
        }
    }

    public function created(Car $car): void
    {
        $car->forceFill([
            'stock_number' => $this->generateStockNumber->execute($car),
        ]);

        $car->forceFill([
            'slug' => $this->generateCarSlug->execute($car),
        ])->saveQuietly();
    }
}
