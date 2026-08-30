<?php

namespace App\Filament\Resources\Makes;

use App\Filament\Resources\Makes\Pages\CreateMake;
use App\Filament\Resources\Makes\Pages\EditMake;
use App\Filament\Resources\Makes\Pages\ListMakes;
use App\Filament\Resources\Makes\Pages\ViewMake;
use App\Filament\Resources\Makes\Schemas\MakeForm;
use App\Filament\Resources\Makes\Schemas\MakeInfolist;
use App\Filament\Resources\Makes\Tables\MakesTable;
use App\Models\Make;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MakeResource extends Resource
{
    protected static ?string $model = Make::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Vehicle Setup';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MakeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MakeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MakesTable::configure($table);
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
            'index' => ListMakes::route('/'),
            'create' => CreateMake::route('/create'),
            'view' => ViewMake::route('/{record}'),
            'edit' => EditMake::route('/{record}/edit'),
        ];
    }
}
