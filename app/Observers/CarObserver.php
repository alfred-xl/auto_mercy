<?php

namespace App\Observers;

use App\Actions\Cars\GenerateCarSlug;
use App\Actions\Cars\GenerateStockNumber;
use App\Enums\CarStatus;
use App\Models\Car;
use Illuminate\Support\Str;
use LogicException;

class CarObserver
{
    public function __construct(
        private readonly GenerateStockNumber $generateStockNumber,
        private readonly GenerateCarSlug $generateCarSlug,
    ) {}

    public function creating(Car $car): void
    {
        if ($car->status !== null && $car->status !== CarStatus::Draft) {
            throw new LogicException('Cars must be created in Draft status.');
        }

        $temporaryIdentity = 'pending-'.Str::lower(Str::random(10));
        $car->stock_number ??= $temporaryIdentity;
        $car->slug ??= $temporaryIdentity;
    }

    public function updating(Car $car): void
    {
        if ($car->isDirty('status') && ! $car->statusTransitionInProgress) {
            throw new LogicException('Car status changes must use the status transition action.');
        }
    }

    public function created(Car $car): void
    {
        if (str_starts_with($car->stock_number, 'pending-')) {
            $car->forceFill([
                'stock_number' => $this->generateStockNumber->execute($car),
            ]);
        }

        $car->forceFill([
            'slug' => $this->generateCarSlug->execute($car),
        ])->saveQuietly();
    }
}
