<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lead overview')->columns(3)->schema([
                TextEntry::make('customer_name')->label('Customer'), TextEntry::make('status')->badge(), TextEntry::make('source')->badge(),
                TextEntry::make('phone')->copyable(), TextEntry::make('email')->placeholder('—')->copyable(), TextEntry::make('car.display_name')->label('Interested vehicle')->placeholder('General enquiry'),
            ]),
            Section::make('Follow-up')->columns(3)->schema([
                TextEntry::make('follow_up_at')->dateTime()->placeholder('—'), TextEntry::make('inspection_at')->dateTime()->placeholder('—'), TextEntry::make('created_at')->dateTime(),
            ]),
            Section::make('Enquiry and notes')->schema([
                TextEntry::make('message')->placeholder('—')->columnSpanFull(), TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }
}
