<?php

namespace App\Enums;

enum VehicleCondition: string
{
    case ForeignUsed = 'foreign_used';

    public function label(): string
    {
        return 'Foreign Used';
    }
}
