<?php

namespace App\Enums;

enum DrivetrainType: string
{
    case Fwd = 'fwd';
    case Rwd = 'rwd';
    case Awd = 'awd';
    case FourWheelDrive = '4wd';

    public function label(): string
    {
        return match ($this) {
            self::Fwd => 'Front-Wheel Drive',
            self::Rwd => 'Rear-Wheel Drive',
            self::Awd => 'All-Wheel Drive',
            self::FourWheelDrive => 'Four-Wheel Drive',
        };
    }
}
