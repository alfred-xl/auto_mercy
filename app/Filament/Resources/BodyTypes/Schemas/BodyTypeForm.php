<?php

namespace App\Filament\Resources\BodyTypes\Schemas;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Schemas\Schema;

class BodyTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return ReferenceResourceDefinition::simpleForm($schema);
    }
}
