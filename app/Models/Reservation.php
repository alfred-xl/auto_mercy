<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['car_id', 'lead_id', 'deposit_amount', 'deposit_confirmed_at', 'expires_at', 'status', 'notes'])]
class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    protected function casts(): array
    {
        return [
            'status' => ReservationStatus::class,
            'deposit_confirmed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
