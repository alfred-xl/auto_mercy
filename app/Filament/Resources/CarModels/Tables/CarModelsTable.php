<?php

namespace App\Filament\Resources\CarModels\Tables;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Tables\Table;

class CarModelsTable
{
    public static function configure(Table $table): Table
    {
        return ReferenceResourceDefinition::simpleTable($table, withMake: true);
    }
}
