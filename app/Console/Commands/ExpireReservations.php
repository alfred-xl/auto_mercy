<?php

namespace App\Console\Commands;

use App\Actions\Reservations\CloseReservation;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('reservations:expire')]
#[Description('Expire active reservations whose confirmed deposit period has elapsed')]
class ExpireReservations extends Command
{
    public function handle(CloseReservation $closeReservation): int
    {
        $expired = 0;

        Reservation::query()
            ->where('status', ReservationStatus::Active)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->orderBy('id')
            ->eachById(function (Reservation $reservation) use ($closeReservation, &$expired): void {
                $closed = $closeReservation->execute($reservation, ReservationStatus::Expired);

                if ($closed->status === ReservationStatus::Expired) {
                    $expired++;
                }
            });

        $this->info("Expired {$expired} overdue reservation(s).");

        return self::SUCCESS;
    }
}
