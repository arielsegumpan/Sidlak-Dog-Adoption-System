<?php

namespace App\Providers;

use App\Filament\Resources\User\UserResource;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Livewire\DatabaseNotifications;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DatabaseNotifications::trigger('filament.notifications.database-notifications-trigger');

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                     ->label('News and Events')
                     ->collapsed(),
                NavigationGroup::make()
                    ->label('Users & Roles')
                    ->collapsed(),
            ]);
        });
    }
}
