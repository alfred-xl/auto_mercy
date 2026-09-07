<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservationAttentionOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active reservations', Reservation::query()->where('status', ReservationStatus::Active)->count())->color('warning'),
            Stat::make('Reservations nearing expiry', Reservation::query()->where('status', ReservationStatus::Active)->whereBetween('expires_at', [now(), now()->addDays(3)])->count())->color('danger'),
        ];
    }
}
