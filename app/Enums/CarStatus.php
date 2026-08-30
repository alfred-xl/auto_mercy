<?php

namespace App\Enums;

enum CarStatus: string
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
