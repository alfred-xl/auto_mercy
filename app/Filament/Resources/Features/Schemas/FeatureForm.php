<?php

namespace App\Filament\Resources\Features\Schemas;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Schemas\Schema;

class FeatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return ReferenceResourceDefinition::simpleForm($schema, category: true);
    }
}
