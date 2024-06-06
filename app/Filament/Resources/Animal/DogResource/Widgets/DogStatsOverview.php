<?php

namespace App\Filament\Resources\Animal\DogResource\Widgets;
use App\Models\Animal\Dog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DogStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $getAdopted = Dog::where('status', 'adopted')->count();
        $getAvailable = Dog::where('status', 'available')->count();

        return [
            Stat::make('Dogs', Dog::all()->count())
            ->color('primary'),
            Stat::make('Available for adoption',  $getAvailable)->color('success'),
            Stat::make('Total Adopted', $getAdopted)->color('success'),
        ];
    }
}
