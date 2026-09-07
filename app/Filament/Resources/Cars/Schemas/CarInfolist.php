<?php

namespace App\Filament\Resources\Cars\Schemas;

use App\Actions\Cars\EnsureCarReadyForPublication;
use App\Models\Car;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class CarInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Callout::make(fn (Car $record): string => (app(EnsureCarReadyForPublication::class)->isReady($record) ? 'Ready to publish' : 'Not ready to publish').' · '.$record->images->count().' gallery image(s)')
                ->status(fn (Car $record): string => app(EnsureCarReadyForPublication::class)->isReady($record) ? 'success' : 'warning')
                ->columnSpanFull(),
            Section::make('Vehicle gallery')
                ->icon('heroicon-o-photo')
                ->schema([
                    View::make('filament.resources.cars.components.gallery'),
                ])
                ->columnSpanFull(),
            Grid::make(['default' => 1, 'xl' => 2])->schema([
                Section::make('Basic vehicle information')
                    ->icon('heroicon-o-truck')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        TextEntry::make('year'),
                        TextEntry::make('make'),
                        TextEntry::make('model'),
                        TextEntry::make('trim')->placeholder('—'),
                        TextEntry::make('body_type')->label('Body type')->placeholder('—'),
                    ]),
                Section::make('Pricing and category')
                    ->icon('heroicon-o-banknotes')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        TextEntry::make('price_amount')->label('Current price')->money('NGN')
                            ->color('primary')->weight(FontWeight::Bold)->size(TextSize::Large),
                        TextEntry::make('previous_price_amount')->label('Previous price')->money('NGN')->placeholder('—'),
                        TextEntry::make('listing_category')->label('Category')->badge(),
                    ]),
            ])->columnSpanFull(),
            Section::make('Specifications and features')
                ->icon('heroicon-o-wrench-screwdriver')
                ->columns(['default' => 1, 'sm' => 2, 'xl' => 4])
                ->schema([
                    TextEntry::make('mileage_display')->label('Mileage')
                        ->state(fn (Car $record): ?string => $record->mileage === null ? null : number_format($record->mileage).' '.($record->mileage_unit?->value ?? ''))
                        ->placeholder('—'),
                    TextEntry::make('transmission')->formatStateUsing(fn ($state): ?string => $state?->label())->placeholder('—'),
                    TextEntry::make('fuel_type')->label('Fuel type')->formatStateUsing(fn ($state): ?string => $state?->label())->placeholder('—'),
                    TextEntry::make('drivetrain')->formatStateUsing(fn ($state): ?string => $state?->label())->placeholder('—'),
                    TextEntry::make('engine')->placeholder('—'),
                    TextEntry::make('exterior_colour')->label('Exterior colour')->placeholder('—'),
                    TextEntry::make('interior_colour')->label('Interior colour')->placeholder('—'),
                    TextEntry::make('features')->badge()->placeholder('No features added')->columnSpanFull(),
                ])
                ->columnSpanFull(),
            Section::make('Description')
                ->icon('heroicon-o-document-text')
                ->schema([
                    TextEntry::make('description')->hiddenLabel()->html(),
                ])
                ->columnSpanFull(),
            Section::make('Listing status and dates')
                ->icon('heroicon-o-signal')
                ->columns(['default' => 1, 'sm' => 2, 'xl' => 4])
                ->schema([
                    TextEntry::make('status')->badge(),
                    IconEntry::make('is_featured')->label('Featured')->boolean(),
                    TextEntry::make('created_at')->label('Created')->dateTime('d M Y, g:i A'),
                    TextEntry::make('updated_at')->label('Last updated')->dateTime('d M Y, g:i A'),
                    TextEntry::make('sold_at')->label('Sold')->dateTime('d M Y, g:i A')->placeholder('—'),
                ])
                ->columnSpanFull(),
        ]);
    }
}
