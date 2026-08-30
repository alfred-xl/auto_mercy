<?php

namespace App\Enums;

enum TransmissionType: string
{
    case Automatic = 'automatic';
    case Manual = 'manual';
    case Cvt = 'cvt';

    public function label(): string
    {
        return match ($this) {
            self::Automatic => 'Automatic',
            self::Manual => 'Manual',
            self::Cvt => 'CVT',
        };
    }
}
