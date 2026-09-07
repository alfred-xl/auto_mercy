<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CarStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Available => 'Available',
            self::Reserved => 'Reserved',
            self::Sold => 'Sold',
            self::Archived => 'Archived',
        };
    }

    public function getLabel(): ?string
    {
        return $this->label();
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Available => 'success',
            self::Reserved => 'warning',
            self::Sold => 'info',
            self::Archived => 'gray',
        };
    }

    /** @return list<self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Available, self::Archived],
            self::Available => [self::Draft, self::Reserved, self::Sold, self::Archived],
            self::Reserved => [self::Available, self::Sold, self::Archived],
            self::Sold => [self::Archived],
            self::Archived => [self::Draft],
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->allowedTransitions(), true);
    }
}
