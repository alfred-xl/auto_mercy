<?php

namespace App\Filament\Resources\Cars\Actions;

use App\Actions\Cars\DeleteCar;
use App\Models\Car;
use DomainException;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Throwable;

class CarDeleteAction
{
    public static function make(): DeleteAction
    {
        return DeleteAction::make()
            ->label('Delete')
            ->authorize('delete')
            ->modalHeading(fn (Car $record): string => 'Delete '.$record->display_name.'?')
            ->modalDescription(fn (Car $record): string => 'You are about to permanently delete '.$record->display_name.' and all of its gallery images. This action cannot be undone.')
            ->modalSubmitActionLabel('Delete vehicle')
            ->successNotificationTitle('Vehicle deleted')
            ->using(fn (Car $record): bool => self::delete($record));
    }

    private static function delete(Car $record): bool
    {
        try {
            app(DeleteCar::class)->execute($record);

            return true;
        } catch (Throwable $exception) {
            if (! $exception instanceof DomainException) {
                report($exception);
            }

            Notification::make()
                ->title('Vehicle could not be deleted')
                ->body($exception instanceof DomainException
                    ? $exception->getMessage()
                    : 'An unexpected error occurred while deleting the vehicle. Please try again.')
                ->danger()
                ->persistent()
                ->send();

            return false;
        }
    }
}
