<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', Models\User::all()->count()),
            Stat::make('Pages', Models\Page::all()->count()),
            Stat::make('Posts', Models\Post::all()->count()),
        ];
    }
}
