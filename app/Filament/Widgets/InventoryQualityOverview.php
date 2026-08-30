<?php

namespace App\Filament\Widgets;

use App\Models\Car;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryQualityOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Missing primary image', Car::query()->whereNull('primary_image_id')->count())->color('warning'),
            Stat::make('Missing description', Car::query()->where(fn ($query) => $query->whereNull('description')->orWhere('description', ''))->count())->color('warning'),
            Stat::make('Missing SEO title', Car::query()->where(fn ($query) => $query->whereNull('meta_title')->orWhere('meta_title', ''))->count()),
            Stat::make('Missing SEO description', Car::query()->where(fn ($query) => $query->whereNull('meta_description')->orWhere('meta_description', ''))->count()),
            Stat::make('Featured inventory', Car::query()->where('is_featured', true)->count())->color('success'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->isAdministrator() ?? false;
    }
}
