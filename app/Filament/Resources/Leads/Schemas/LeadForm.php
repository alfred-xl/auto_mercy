<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Car;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Customer')->columns(2)->schema([
                TextInput::make('customer_name')->required()->maxLength(255),
                TextInput::make('phone')->tel()->required()->maxLength(32),
                TextInput::make('email')->email()->maxLength(255),
                Select::make('source')->options(self::sourceOptions())->default(LeadSource::Website->value)->required(),
            ]),
            Section::make('Enquiry')->columns(2)->schema([
                Select::make('car_id')->label('Interested vehicle')->relationship('car', 'stock_number')->getOptionLabelFromRecordUsing(fn (Car $record): string => $record->display_name)->searchable(['stock_number', 'make', 'model'])->preload(),
                Select::make('status')->options(self::statusOptions())->default(LeadStatus::New->value)->required(),
                Textarea::make('message')->rows(4)->columnSpanFull(),
            ]),
            Section::make('Follow-up')->columns(2)->schema([
                DateTimePicker::make('follow_up_at')->label('Next follow-up')->seconds(false),
                DateTimePicker::make('inspection_at')->label('Inspection date')->seconds(false),
                Textarea::make('notes')->rows(5)->columnSpanFull(),
            ]),
        ]);
    }

    public static function statusOptions(): array
    {
        return collect(LeadStatus::cases())->mapWithKeys(fn (LeadStatus $status): array => [$status->value => $status->label()])->all();
    }

    private static function sourceOptions(): array
    {
        return collect(LeadSource::cases())->mapWithKeys(fn (LeadSource $source): array => [$source->value => $source->label()])->all();
    }
}
