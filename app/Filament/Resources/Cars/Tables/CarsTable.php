<?php

namespace App\Filament\Resources\Cars\Tables;

use App\Enums\CarStatus;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CarsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('updated_at', 'desc')->columns([
            TextColumn::make('stock_number')->label('Stock')->searchable()->sortable()->copyable(),
            TextColumn::make('make.name')->searchable()->sortable(),
            TextColumn::make('carModel.name')->label('Model')->searchable()->sortable(),
            TextColumn::make('year')->sortable(),
            TextColumn::make('price_amount')->label('Price')->money('NGN')->sortable(),
            TextColumn::make('status')->badge()->formatStateUsing(fn (CarStatus $state): string => $state->label())->color(fn (CarStatus $state): string => match ($state) {
                CarStatus::Available => 'success', CarStatus::Reserved => 'warning', CarStatus::Sold => 'info', CarStatus::Archived => 'gray', default => 'primary'
            }),
            TextColumn::make('carStand.name')->label('Stand')->toggleable(),
            IconColumn::make('is_featured')->label('Featured')->boolean()->toggleable(),
            TextColumn::make('updated_at')->since()->sortable()->toggleable(),
        ])->filters([
            SelectFilter::make('status')->options(collect(CarStatus::cases())->mapWithKeys(fn (CarStatus $status): array => [$status->value => $status->label()])->all()),
            SelectFilter::make('make')->relationship('make', 'name')->searchable()->preload(),
            SelectFilter::make('car_stand')->relationship('carStand', 'name')->searchable()->preload(),
            TrashedFilter::make(),
        ])->recordActions([ViewAction::make(), EditAction::make(), RestoreAction::make()])
            ->emptyStateHeading('No vehicles yet')->emptyStateDescription('Create a draft vehicle record to begin building inventory.');
    }
}
