<?php

namespace App\Filament\Resources\Makes\Tables;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Tables\Table;

class MakesTable
{
    public static function configure(Table $table): Table
    {
        return ReferenceResourceDefinition::simpleTable($table);
    }
}
