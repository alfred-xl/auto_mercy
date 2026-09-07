<?php

namespace App\Filament\Resources\Cars\Pages;

use App\Filament\Resources\Cars\Actions\CarDeleteAction;
use App\Filament\Resources\Cars\Actions\CarLifecycleActions;
use App\Filament\Resources\Cars\CarResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCar extends ViewRecord
{
    protected static string $resource = CarResource::class;

    protected function getHeaderActions(): array
    {
        $actions = collect(CarLifecycleActions::make())->keyBy(fn ($action): string => $action->getName());
        $statusActions = collect(['publish', 'mark_available', 'mark_sold'])
            ->map(fn (string $name) => $actions->pull($name))
            ->filter()
            ->values()
            ->all();

        return [
            EditAction::make(),
            ...$statusActions,
            CarDeleteAction::make(),
            ActionGroup::make($actions->values()->all())->label('More actions')->icon('heroicon-m-ellipsis-vertical')->button()->color('gray'),
        ];
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
