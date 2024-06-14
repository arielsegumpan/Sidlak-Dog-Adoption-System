<?php

namespace App\Filament\Resources\Animal\DogResource\Widgets;

use App\Filament\Resources\Animal\DogResource\Pages\ListDogs;
use App\Models\Animal\Dog;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DogStatsOverview extends BaseWidget
{

    protected static ?string $pollingInterval = null;


    protected function getStats(): array
    {

        $dogStats = Dog::query()
            ->select([
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available"),
                DB::raw("SUM(CASE WHEN status = 'adopted' THEN 1 ELSE 0 END) as adopted"),
            ])
            ->first();

        $adoptedData = Trend::model(Dog::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        // Calculate the current and previous month adoption counts
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthAdoptions = Dog::where('status', 'adopted')
            ->where('created_at', '>=', $currentMonth)
            ->count();

        $previousMonthAdoptions = Dog::where('status', 'adopted')
            ->where('created_at', '>=', $previousMonth)
            ->where('created_at', '<', $currentMonth)
            ->count();

        [$adoptionIncreasePercentage, $direction]  = $this->calculatePercentageIncrease($previousMonthAdoptions, $currentMonthAdoptions);

        return [
            Stat::make(label: 'Dogs', value: $dogStats->total)
            ->color('primary'),
            Stat::make(label: 'Available for adoption', value: $dogStats->available)
                ->color('success'),
            Stat::make(label: 'Total Dogs Adopted', value: $dogStats->adopted)
                ->color('success')
                ->chart(
                    $adoptedData
                        ->map(fn (TrendValue $value) => $value->aggregate)
                        ->toArray()
                )
                ->description($adoptionIncreasePercentage . '% ' . $direction)
                ->descriptionIcon($adoptionIncreasePercentage > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($adoptionIncreasePercentage > 0 ? 'success' : 'danger'),
        ];
    }


    protected function calculatePercentageIncrease($previous, $current)
    {
        if ($previous == 0) {
            return [$current > 0 ? 100 : 0, $current > 0 ? 'increase' : 'no change'];
        }

        $increase = (($current - $previous) / $previous) * 100;
        $direction = $increase > 0 ? ' increase' : ' decrease';

        return number_format($increase, 2);
    }
}
