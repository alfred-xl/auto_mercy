<?php

namespace App\Actions\Reservations;

use App\Enums\CarStatus;
use App\Enums\ReservationStatus;
use App\Models\Car;
use App\Models\Reservation;
use DomainException;
use Illuminate\Support\Facades\DB;

class CloseReservation
{
    public function execute(Reservation $reservation, ReservationStatus $target): Reservation
    {
        if (! $target->isClosed()) {
            throw new DomainException('Choose a closed reservation status.');
        }

        return DB::transaction(function () use ($reservation, $target): Reservation {
            $lockedReservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->getKey());

            if ($lockedReservation->status !== ReservationStatus::Active) {
                return $lockedReservation;
            }

            $car = Car::query()->lockForUpdate()->findOrFail($lockedReservation->car_id);
            $lockedReservation->update(['status' => $target]);

            if (! in_array($car->status, [CarStatus::Sold, CarStatus::Archived], true)) {
                $car->statusTransitionInProgress = true;
                $car->update([
                    'status' => $target === ReservationStatus::Completed ? CarStatus::Sold : CarStatus::Available,
                    'sold_at' => $target === ReservationStatus::Completed ? now() : null,
                ]);
            }

            return $lockedReservation->refresh();
        });
    }
}
