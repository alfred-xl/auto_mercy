<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Actions\Reservations\CreateReservation as CreateReservationAction;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Car;
use App\Models\Lead;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateReservationAction::class)->execute(
            Car::query()->findOrFail($data['car_id']),
            filled($data['lead_id'] ?? null) ? Lead::query()->findOrFail($data['lead_id']) : null,
            (int) $data['deposit_amount'],
            $data['notes'] ?? null,
        );
    }
}
