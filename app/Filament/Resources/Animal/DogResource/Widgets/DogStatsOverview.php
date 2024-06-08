<?php

namespace App\Filament\Resources\Animal\DogResource\Widgets;
use App\Models\Animal\Dog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class DogStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get all counts in a single query using a case statement
        $dogCounts = Dog::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'adopted' THEN 1 ELSE 0 END) as adopted,
            SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available
        ")->first();

        $adoptedData = Trend::model(Dog::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            Stat::make('Dogs', $dogCounts->total)
                ->color('primary'),
            Stat::make('Available for adoption', $dogCounts->available)
                ->color('success'),
            Stat::make('Total Dogs Adopted', $dogCounts->adopted)
                ->color('success')
                ->chart(
                    $adoptedData
                        ->map(fn (TrendValue $value) => $value->aggregate)
                        ->toArray()
                ),
        ];
    }
}
