<?php

namespace App\Filament\Widgets;

use App\Enums\CarStatus;
use App\Models\Car;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservationAttentionOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $reserved = Car::query()->where('status', CarStatus::Reserved);

        return [
            Stat::make('Overdue reservations', (clone $reserved)->where('reservation_expires_at', '<', now())->count())->color('danger'),
            Stat::make('Expiring within 7 days', (clone $reserved)->whereBetween('reservation_expires_at', [now(), now()->addDays(7)])->count())->color('warning'),
            Stat::make('Reserved this week', (clone $reserved)->where('reserved_at', '>=', now()->subDays(7))->count())->color('info'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->isAdministrator() ?? false;
    }
}
