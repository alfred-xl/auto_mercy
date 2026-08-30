<?php

namespace App\Filament\Resources\BodyTypes\Tables;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Tables\Table;

class BodyTypesTable
{
    public static function configure(Table $table): Table
    {
        return ReferenceResourceDefinition::simpleTable($table);
    }
}
