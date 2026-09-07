<?php

namespace App\Actions\Cars;

use App\Enums\CarStatus;
use App\Models\Car;
use DomainException;
use Illuminate\Support\Facades\DB;

class TransitionCarStatus
{
    public function __construct(private readonly EnsureCarReadyForPublication $readiness) {}

    public function execute(Car $car, CarStatus $target): Car
    {
        return DB::transaction(function () use ($car, $target): Car {
            $lockedCar = Car::query()->lockForUpdate()->findOrFail($car->getKey());

            if (! $lockedCar->status->canTransitionTo($target)) {
                throw new DomainException("A car cannot transition from {$lockedCar->status->label()} to {$target->label()}.");
            }

            if ($target === CarStatus::Available) {
                $this->readiness->execute($lockedCar);
            }

            $lockedCar->statusTransitionInProgress = true;
            $lockedCar->forceFill([
                'status' => $target,
                'sold_at' => $target === CarStatus::Sold ? now() : null,
            ])->save();
            $lockedCar->statusTransitionInProgress = false;

            return $lockedCar->refresh();
        });
    }
}
