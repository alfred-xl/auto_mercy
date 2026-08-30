<?php

namespace App\Filament\Resources\CarStands;

use App\Filament\Resources\CarStands\Pages\CreateCarStand;
use App\Filament\Resources\CarStands\Pages\EditCarStand;
use App\Filament\Resources\CarStands\Pages\ListCarStands;
use App\Filament\Resources\CarStands\Pages\ViewCarStand;
use App\Filament\Resources\CarStands\Schemas\CarStandForm;
use App\Filament\Resources\CarStands\Schemas\CarStandInfolist;
use App\Filament\Resources\CarStands\Tables\CarStandsTable;
use App\Models\CarStand;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CarStandResource extends Resource
{
    protected static ?string $model = CarStand::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'Vehicle Setup';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CarStandForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CarStandInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarStandsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarStands::route('/'),
            'create' => CreateCarStand::route('/create'),
            'view' => ViewCarStand::route('/{record}'),
            'edit' => EditCarStand::route('/{record}/edit'),
        ];
    }
}
