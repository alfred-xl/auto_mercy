<?php

namespace App\Actions\Reservations;

use App\Enums\CarStatus;
use App\Enums\ReservationStatus;
use App\Models\Car;
use App\Models\Lead;
use App\Models\Reservation;
use DomainException;
use Illuminate\Support\Facades\DB;

class CreateReservation
{
    public function execute(Car $car, ?Lead $lead, int $depositAmount, ?string $notes = null): Reservation
    {
        return DB::transaction(function () use ($car, $lead, $depositAmount, $notes): Reservation {
            $lockedCar = Car::query()->lockForUpdate()->findOrFail($car->getKey());

            if ($lockedCar->reservations()->where('status', ReservationStatus::Active)->exists()) {
                throw new DomainException('This vehicle already has an active reservation.');
            }

            if ($lockedCar->status !== CarStatus::Available) {
                throw new DomainException('Only an available vehicle can be reserved.');
            }

            $confirmedAt = now();
            $reservation = $lockedCar->reservations()->create([
                'lead_id' => $lead?->getKey(),
                'deposit_amount' => $depositAmount,
                'deposit_confirmed_at' => $confirmedAt,
                'expires_at' => $confirmedAt->copy()->addDays((int) config('automercy.reservation.duration_days')),
                'status' => ReservationStatus::Active,
                'notes' => $notes,
            ]);

            $lockedCar->statusTransitionInProgress = true;
            $lockedCar->update(['status' => CarStatus::Reserved]);

            return $reservation;
        });
    }
}
