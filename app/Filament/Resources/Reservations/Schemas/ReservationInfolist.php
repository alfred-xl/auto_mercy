<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Reservation')->columns(3)->schema([
                TextEntry::make('car.display_name')->label('Vehicle'), TextEntry::make('lead.display_name')->label('Customer')->placeholder('—'), TextEntry::make('status')->badge(),
                TextEntry::make('deposit_amount')->money('NGN'), TextEntry::make('deposit_confirmed_at')->dateTime()->placeholder('—'), TextEntry::make('expires_at')->dateTime()->placeholder('—'),
                TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }
}
