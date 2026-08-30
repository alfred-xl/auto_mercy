<?php

namespace App\Filament\Resources\Makes\Schemas;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Schemas\Schema;

class MakeForm
{
    public static function configure(Schema $schema): Schema
    {
        return ReferenceResourceDefinition::simpleForm($schema);
    }
}
