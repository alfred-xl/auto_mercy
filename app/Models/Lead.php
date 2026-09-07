<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['car_id', 'customer_name', 'phone', 'email', 'message', 'source', 'status', 'follow_up_at', 'inspection_at', 'notes'])]
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory;

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->customer_name.' · '.$this->phone;
    }

    protected function casts(): array
    {
        return [
            'source' => LeadSource::class,
            'status' => LeadStatus::class,
            'follow_up_at' => 'datetime',
            'inspection_at' => 'datetime',
        ];
    }
}
