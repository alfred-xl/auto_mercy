<?php

namespace App\Filament\Resources\Features\Schemas;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Schemas\Schema;

class FeatureInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return ReferenceResourceDefinition::simpleInfolist($schema, category: true);
    }
}
