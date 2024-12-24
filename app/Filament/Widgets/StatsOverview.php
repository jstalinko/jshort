<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Shortlink;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users' , User::count()),
            Stat::make('Shortinks' , Shortlink::count()),
            Stat::make('Traffic' , Shortlink::sum('total_allowed') + Shortlink::sum('total_blocked'))
        ];
    }
}
