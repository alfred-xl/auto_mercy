<?php

namespace App\Filament\Resources\CarStands\Tables;

use App\Filament\Support\ReferenceResourceDefinition;
use Filament\Tables\Table;

class CarStandsTable
{
    public static function configure(Table $table): Table
    {
        return ReferenceResourceDefinition::standTable($table);
    }
}
