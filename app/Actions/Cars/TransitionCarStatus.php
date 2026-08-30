<?php

namespace App\Actions\Cars;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\User;
use Carbon\CarbonInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

class TransitionCarStatus
{
    public function __construct(
        private readonly EnsureCarReadyForPublication $ensureCarReadyForPublication,
    ) {}

    public function execute(
        Car $car,
        CarStatus $target,
        ?CarbonInterface $reservationExpiresAt = null,
        ?User $actor = null,
    ): Car {
        return DB::transaction(function () use ($car, $target, $reservationExpiresAt, $actor): Car {
            $lockedCar = Car::query()->lockForUpdate()->findOrFail($car->getKey());
            $current = $lockedCar->status;

            if (! $current->canTransitionTo($target)) {
                throw new DomainException("A car cannot transition from {$current->label()} to {$target->label()}.");
            }

            if ($current === CarStatus::Draft && $target === CarStatus::Available) {
                $this->ensureCarReadyForPublication->execute($lockedCar);
            }

            if ($target === CarStatus::Reserved && $reservationExpiresAt?->lessThanOrEqualTo(now())) {
                throw new DomainException('The reservation expiry must be in the future.');
            }

            $attributes = $this->lifecycleAttributes($lockedCar, $target, $reservationExpiresAt);

            $lockedCar->statusTransitionInProgress = true;
            $lockedCar->forceFill([
                'status' => $target,
                ...$attributes,
                ...($actor === null ? [] : ['updated_by' => $actor->getKey()]),
            ])->save();
            $lockedCar->statusTransitionInProgress = false;

            return $lockedCar->refresh();
        });
    }

    /** @return array<string, CarbonInterface|null> */
    private function lifecycleAttributes(
        Car $car,
        CarStatus $target,
        ?CarbonInterface $reservationExpiresAt,
    ): array {
        return match ($target) {
            CarStatus::Draft => [
                'published_at' => null,
                'reserved_at' => null,
                'reservation_expires_at' => null,
                'archived_at' => null,
            ],
            CarStatus::Available => [
                'published_at' => $car->published_at ?? now(),
                'reserved_at' => null,
                'reservation_expires_at' => null,
                'archived_at' => null,
            ],
            CarStatus::Reserved => [
                'reserved_at' => now(),
                'reservation_expires_at' => $reservationExpiresAt ?? now()->addDays((int) config('automercy.reservation.duration_days')),
            ],
            CarStatus::Sold => [
                'sold_at' => now(),
                'reservation_expires_at' => null,
            ],
            CarStatus::Archived => [
                'archived_at' => now(),
                'reservation_expires_at' => null,
            ],
        };
    }
}
