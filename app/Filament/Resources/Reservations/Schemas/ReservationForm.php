<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\Lead;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Confirmed deposit')->description('Use this only after staff has confirmed the customer deposit.')->columns(2)->schema([
                Select::make('car_id')->label('Vehicle')->relationship('car', 'stock_number', modifyQueryUsing: fn (Builder $query): Builder => $query->where('status', CarStatus::Available))->getOptionLabelFromRecordUsing(fn (Car $record): string => $record->display_name)->searchable(['stock_number', 'make', 'model'])->preload()->required()->disabledOn('edit'),
                Select::make('lead_id')->label('Customer lead')->relationship('lead', 'customer_name')->getOptionLabelFromRecordUsing(fn (Lead $record): string => $record->display_name)->searchable(['customer_name', 'phone'])->preload()->disabledOn('edit'),
                TextInput::make('deposit_amount')->numeric()->prefix('₦')->default((int) config('automercy.reservation.amount'))->required()->disabledOn('edit'),
                Textarea::make('notes')->rows(4)->columnSpanFull(),
            ]),
        ]);
    }
}
