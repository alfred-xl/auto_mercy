<?php

namespace App\Filament\Widgets;

use App\Enums\CarStatus;
use App\Models\Car;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatusOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Available vehicles', Car::query()->where('status', CarStatus::Available)->count())->color('success'),
            Stat::make('Reserved vehicles', Car::query()->where('status', CarStatus::Reserved)->count())->color('warning'),
            Stat::make('Sold vehicles', Car::query()->where('status', CarStatus::Sold)->count())->color('info'),
        ];
    }
}
