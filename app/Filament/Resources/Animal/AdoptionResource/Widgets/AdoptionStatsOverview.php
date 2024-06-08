<?php

namespace App\Filament\Resources\Animal\AdoptionResource\Widgets;

use App\Models\Adoption\Adoption;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AdoptionStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get all counts in a single query using a case statement
        $adoptionCounts = Adoption::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        ")->first();

        $adoptionData = Trend::model(Adoption::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            Stat::make('Total adoption requests', $adoptionCounts->total)
                ->color('success')
                ->chart(
                    $adoptionData
                        ->map(fn (TrendValue $value) => $value->aggregate)
                        ->toArray()
                ),
            Stat::make('Pending', $adoptionCounts->pending)->color('warning'),
            Stat::make('Approved', $adoptionCounts->approved)->color('success'),
            Stat::make('Rejected', $adoptionCounts->rejected)->color('danger'),
        ];
    }
    // protected function getStats(): array
    // {
    //     $getPending = Adoption::where('status', 'pending')->count();
    //     $getApproved = Adoption::where('status', 'approved')->count();
    //     $getRejected = Adoption::where('status', 'rejected')->count();

    //     $adoptionData = Trend::model(Adoption::class)
    //     ->between(
    //         start: now()->subYear(),
    //         end: now(),
    //     )
    //     ->perMonth()
    //     ->count();

    //     return [
    //         Stat::make('Total adoption requests', Adoption::all()->count())
    //         ->color('success')
    //         ->chart(
    //             $adoptionData
    //                 ->map(fn (TrendValue $value) => $value->aggregate)
    //                 ->toArray()
    //         ),
    //         Stat::make('Pending',  $getPending)->color('warning'),
    //         Stat::make('Approved', $getApproved)->color('success'),
    //         Stat::make('Rejected', $getRejected)->color('danger'),
    //     ];
    // }
}
