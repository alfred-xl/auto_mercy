<?php

namespace App\Filament\Resources\Cars\Actions;

use App\Actions\Cars\DuplicateCarAsDraft;
use App\Actions\Cars\TransitionCarStatus;
use App\Enums\CarStatus;
use App\Filament\Resources\Cars\CarResource;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use DomainException;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;

class CarLifecycleActions
{
    /** @return list<Action> */
    public static function make(): array
    {
        return [
            self::transition('publish', 'Publish', CarStatus::Draft, CarStatus::Available, 'success', 'Publish this vehicle to available inventory?'),
            Action::make('reserve')
                ->label('Reserve')
                ->color('warning')
                ->visible(fn (Car $record): bool => $record->status === CarStatus::Available && auth()->user()?->can('transitionStatus', $record))
                ->schema([
                    DateTimePicker::make('reservation_expires_at')
                        ->label('Reservation expires at')
                        ->default(fn () => now()->addDays((int) config('automercy.reservation.duration_days')))
                        ->after('now')
                        ->required(),
                ])
                ->modalDescription(fn (): string => 'Reservation amount: ₦'.number_format((int) config('automercy.reservation.amount')).'. Default duration: '.config('automercy.reservation.duration_days').' days.')
                ->action(function (Car $record, array $data): void {
                    self::runTransition($record, CarStatus::Reserved, Carbon::parse($data['reservation_expires_at']));
                }),
            self::transition('return_available', 'Return to available', CarStatus::Reserved, CarStatus::Available, 'success', 'End this reservation and make the vehicle available again?'),
            Action::make('mark_sold')
                ->label('Mark sold')->color('info')->requiresConfirmation()
                ->visible(fn (Car $record): bool => in_array($record->status, [CarStatus::Available, CarStatus::Reserved], true) && auth()->user()?->can('transitionStatus', $record))
                ->action(fn (Car $record) => self::runTransition($record, CarStatus::Sold)),
            Action::make('archive')
                ->color('gray')->requiresConfirmation()
                ->visible(fn (Car $record): bool => $record->status !== CarStatus::Archived && $record->status->canTransitionTo(CarStatus::Archived) && auth()->user()?->can('archive', $record))
                ->action(fn (Car $record) => self::runTransition($record, CarStatus::Archived)),
            self::transition('restore_draft', 'Restore to draft', CarStatus::Archived, CarStatus::Draft, 'primary', 'Restore this archived vehicle as a draft?'),
            Action::make('duplicate')
                ->label('Duplicate as draft')
                ->visible(fn (Car $record): bool => auth()->user()?->can('view', $record) && auth()->user()?->can('update', $record) && auth()->user()?->can('create', Car::class))
                ->action(function (Car $record) {
                    $duplicate = app(DuplicateCarAsDraft::class)->execute($record, self::actor());
                    Notification::make()->title('Draft duplicated')->body("Created {$duplicate->stock_number} without images or lifecycle history.")->success()->send();

                    return redirect(CarResource::getUrl('edit', ['record' => $duplicate]));
                }),
        ];
    }

    private static function transition(string $name, string $label, CarStatus $source, CarStatus $target, string $color, string $confirmation): Action
    {
        return Action::make($name)
            ->label($label)->color($color)->requiresConfirmation()->modalDescription($confirmation)
            ->visible(fn (Car $record): bool => $record->status === $source && auth()->user()?->can($target === CarStatus::Available && $source === CarStatus::Draft ? 'publish' : 'transitionStatus', $record))
            ->action(fn (Car $record) => self::runTransition($record, $target));
    }

    private static function runTransition(Car $record, CarStatus $target, ?Carbon $expiresAt = null): void
    {
        try {
            Gate::forUser(self::actor())->authorize($target === CarStatus::Available && $record->status === CarStatus::Draft ? 'publish' : ($target === CarStatus::Archived ? 'archive' : 'transitionStatus'), $record);
            app(TransitionCarStatus::class)->execute($record, $target, $expiresAt, self::actor());
            Notification::make()->title('Vehicle status updated')->body("The vehicle is now {$target->label()}.")->success()->send();
        } catch (DomainException $exception) {
            Notification::make()->title('Status change blocked')->body($exception->getMessage())->danger()->persistent()->send();
        }
    }

    private static function actor(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
