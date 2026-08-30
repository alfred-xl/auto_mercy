<?php

namespace App\Filament\Resources\CarStands\Schemas;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Schemas\Schema;

class CarStandForm
{
    public static function configure(Schema $schema): Schema
    {
        return ReferenceResourceDefinition::standForm($schema);
    }
}
