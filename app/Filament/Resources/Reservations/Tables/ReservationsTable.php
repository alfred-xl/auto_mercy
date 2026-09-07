<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Actions\Reservations\CloseReservation;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            TextColumn::make('car.display_name')->label('Vehicle')->searchable(['stock_number', 'make', 'model'])->wrap(),
            TextColumn::make('lead.display_name')->label('Customer')->placeholder('—'),
            TextColumn::make('deposit_amount')->money('NGN'),
            TextColumn::make('status')->badge(),
            TextColumn::make('deposit_confirmed_at')->label('Confirmed')->dateTime('d M Y, H:i'),
            TextColumn::make('expires_at')->label('Expires')->dateTime('d M Y, H:i')->placeholder('—')->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(collect(ReservationStatus::cases())->mapWithKeys(fn (ReservationStatus $status): array => [$status->value => $status->label()])->all()),
        ])->recordActions([
            ActionGroup::make([
                ViewAction::make(), EditAction::make(),
                self::closeAction('complete', 'Complete sale', ReservationStatus::Completed, 'success'),
                self::closeAction('cancel', 'Cancel reservation', ReservationStatus::Cancelled, 'warning'),
                self::closeAction('forfeit', 'Forfeit deposit', ReservationStatus::Forfeited, 'danger'),
                self::closeAction('expire', 'Mark expired', ReservationStatus::Expired, 'gray'),
            ])->icon('heroicon-m-ellipsis-vertical')->iconButton()->tooltip('Reservation actions'),
        ]);
    }

    private static function closeAction(string $name, string $label, ReservationStatus $status, string $color): Action
    {
        return Action::make($name)->label($label)->color($color)->requiresConfirmation()->visible(fn (Reservation $record): bool => $record->status === ReservationStatus::Active)->action(fn (Reservation $record) => app(CloseReservation::class)->execute($record, $status));
    }
}
