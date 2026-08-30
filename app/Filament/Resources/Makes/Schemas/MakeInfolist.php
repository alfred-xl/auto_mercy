<?php

namespace App\Filament\Resources\Makes\Schemas;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Schemas\Schema;

class MakeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return ReferenceResourceDefinition::simpleInfolist($schema);
    }
}
