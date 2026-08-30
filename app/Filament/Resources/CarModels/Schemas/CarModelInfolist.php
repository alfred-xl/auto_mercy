<?php

namespace App\Filament\Resources\CarModels\Schemas;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Schemas\Schema;

class CarModelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return ReferenceResourceDefinition::simpleInfolist($schema, withMake: true);
    }
}
