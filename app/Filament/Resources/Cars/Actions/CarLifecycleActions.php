<?php

namespace App\Filament\Resources\Cars\Actions;

use App\Actions\Cars\DuplicateCarAsDraft;
use App\Actions\Cars\EnsureCarReadyForPublication;
use App\Actions\Cars\TransitionCarStatus;
use App\Enums\CarStatus;
use App\Filament\Resources\Cars\CarResource;
use App\Models\Car;
use App\Models\User;
use DomainException;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CarLifecycleActions
{
    /** @return list<Action> */
    public static function make(): array
    {
        return [
            Action::make('preview')->label('Preview public page')->icon('heroicon-o-arrow-top-right-on-square')->url(fn (Car $record): string => route('admin.cars.preview', ['car' => $record->getKey()]))->openUrlInNewTab()->visible(fn (Car $record): bool => $record->status !== CarStatus::Archived),
            self::transition('publish', 'Publish', CarStatus::Draft, CarStatus::Available, 'success'),
            self::transitionFrom('mark_sold', 'Mark as sold', [CarStatus::Available, CarStatus::Reserved], CarStatus::Sold, 'danger'),
            self::transition('mark_available', 'Mark as available', CarStatus::Reserved, CarStatus::Available, 'success'),
            Action::make('toggle_feature')->label(fn (Car $record): string => $record->is_featured ? 'Unfeature vehicle' : 'Feature vehicle')->icon('heroicon-o-star')->action(function (Car $record): void {
                $record->update(['is_featured' => ! $record->is_featured]);
                Notification::make()->title($record->is_featured ? 'Vehicle featured' : 'Vehicle unfeatured')->success()->send();
            }),
            Action::make('archive')->requiresConfirmation()->color('gray')->visible(fn (Car $record): bool => $record->status !== CarStatus::Archived && $record->status->canTransitionTo(CarStatus::Archived))->action(fn (Car $record) => self::runTransition($record, CarStatus::Archived)),
            self::transition('restore_draft', 'Restore to draft', CarStatus::Archived, CarStatus::Draft, 'primary'),
            Action::make('duplicate')->label('Duplicate as draft')->action(function (Car $record) {
                $duplicate = app(DuplicateCarAsDraft::class)->execute($record, self::actor());
                Notification::make()->title('Draft duplicated')->success()->send();

                return redirect(CarResource::getUrl('edit', ['record' => $duplicate]));
            }),
        ];
    }

    private static function transition(string $name, string $label, CarStatus $source, CarStatus $target, string $color): Action
    {
        return Action::make($name)->label($label)->color($color)->requiresConfirmation()->visible(fn (Car $record): bool => $record->status === $source)->disabled(fn (Car $record): bool => $target === CarStatus::Available && ! app(EnsureCarReadyForPublication::class)->isReady($record))->action(fn (Car $record) => self::runTransition($record, $target));
    }

    /** @param list<CarStatus> $sources */
    private static function transitionFrom(string $name, string $label, array $sources, CarStatus $target, string $color): Action
    {
        return Action::make($name)
            ->label($label)
            ->color($color)
            ->requiresConfirmation()
            ->visible(fn (Car $record): bool => in_array($record->status, $sources, true) && $record->status->canTransitionTo($target))
            ->action(fn (Car $record) => self::runTransition($record, $target));
    }

    private static function runTransition(Car $record, CarStatus $target): void
    {
        try {
            app(TransitionCarStatus::class)->execute($record, $target);
            Notification::make()->title('Vehicle status updated')->body("The vehicle is now {$target->label()}.")->success()->send();
        } catch (DomainException $exception) {
            Notification::make()->title('Status change blocked')->body($exception->getMessage())->danger()->send();
        }
    }

    private static function actor(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
