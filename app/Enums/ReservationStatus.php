<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ReservationStatus: string implements HasColor, HasLabel
{
    case Active = 'active';
    case Completed = 'completed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
    case Forfeited = 'forfeited';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function getLabel(): ?string
    {
        return $this->label();
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active => 'warning',
            self::Completed => 'success',
            self::Expired => 'gray',
            self::Cancelled => 'danger',
            self::Forfeited => 'danger',
        };
    }

    public function isClosed(): bool
    {
        return $this !== self::Active;
    }
}
