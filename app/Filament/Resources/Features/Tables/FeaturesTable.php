<?php

namespace App\Filament\Resources\Features\Tables;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Tables\Table;

class FeaturesTable
{
    public static function configure(Table $table): Table
    {
        return ReferenceResourceDefinition::simpleTable($table, category: true);
    }
}
