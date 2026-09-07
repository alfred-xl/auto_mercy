<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadOperationsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $openStatuses = [LeadStatus::New, LeadStatus::Contacted, LeadStatus::InspectionScheduled, LeadStatus::Negotiating];

        return [
            Stat::make('New leads', Lead::query()->where('status', LeadStatus::New)->count())->color('danger'),
            Stat::make('Follow-ups due', Lead::query()->whereIn('status', $openStatuses)->whereNotNull('follow_up_at')->where('follow_up_at', '<=', now())->count())->color('warning'),
            Stat::make('Upcoming inspections', Lead::query()->whereBetween('inspection_at', [now(), now()->addDays(7)])->count())->color('info'),
        ];
    }
}
