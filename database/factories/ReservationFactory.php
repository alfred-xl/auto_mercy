<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\Car;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Reservation> */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $confirmedAt = now();

        return [
            'car_id' => Car::factory(),
            'lead_id' => null,
            'deposit_amount' => (int) config('automercy.reservation.amount'),
            'deposit_confirmed_at' => $confirmedAt,
            'expires_at' => $confirmedAt->copy()->addDays((int) config('automercy.reservation.duration_days')),
            'status' => ReservationStatus::Active,
            'notes' => null,
        ];
    }
}
