<?php

namespace App\Filament\Resources\Cars\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CarInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Vehicle')->columns(3)->schema([
                TextEntry::make('stock_number')->label('Stock'), TextEntry::make('status')->badge(), TextEntry::make('year'),
                TextEntry::make('make.name')->label('Make'), TextEntry::make('carModel.name')->label('Model'), TextEntry::make('trim'),
                TextEntry::make('price_amount')->money('NGN'), TextEntry::make('mileage'), TextEntry::make('carStand.name')->label('Stand'),
            ]),
            Section::make('Content')->schema([TextEntry::make('description')->html()->columnSpanFull(), TextEntry::make('features.name')->badge()->separator(',')]),
            Section::make('Lifecycle')->columns(3)->collapsed()->schema([
                TextEntry::make('published_at')->dateTime(), TextEntry::make('reserved_at')->dateTime(), TextEntry::make('reservation_expires_at')->dateTime(),
                TextEntry::make('sold_at')->dateTime(), TextEntry::make('archived_at')->dateTime(), TextEntry::make('updated_at')->dateTime(),
            ]),
        ]);
    }
}
