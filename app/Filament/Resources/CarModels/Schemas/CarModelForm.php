<?php

namespace App\Filament\Resources\CarModels\Schemas;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Schemas\Schema;

class CarModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return ReferenceResourceDefinition::modelForm($schema);
    }
}
