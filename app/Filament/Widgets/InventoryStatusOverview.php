<?php

namespace App\Filament\Widgets;

use App\Enums\CarStatus;
use App\Filament\Resources\Cars\CarResource;
use App\Models\Car;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatusOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $counts = Car::query()->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');

        return collect(CarStatus::cases())->map(fn (CarStatus $status): Stat => Stat::make($status->label(), (int) ($counts[$status->value] ?? 0))
            ->color(match ($status) {
                CarStatus::Available => 'success', CarStatus::Reserved => 'warning', CarStatus::Sold => 'info', CarStatus::Archived => 'gray', default => 'primary'
            })
            ->url(CarResource::getUrl('index', ['tableFilters' => ['status' => ['value' => $status->value]]])))->all();
    }

    public static function canView(): bool
    {
        return auth()->user()?->isAdministrator() ?? false;
    }
}
