<?php

namespace App\Filament\Resources\CarStands\Schemas;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Schemas\Schema;

class CarStandInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return ReferenceResourceDefinition::standInfolist($schema);
    }
}
