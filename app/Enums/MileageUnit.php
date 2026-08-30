<?php

namespace App\Enums;

enum MileageUnit: string
{
    case Kilometres = 'km';
    case Miles = 'mi';

    public function label(): string
    {
        return match ($this) {
            self::Kilometres => 'Kilometres',
            self::Miles => 'Miles',
        };
    }
}
