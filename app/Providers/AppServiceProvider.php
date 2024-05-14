<?php

namespace App\Providers;

use App\Models\AdoptionRequest;
use App\Observers\AdoptionRequestObserver;
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
        AdoptionRequest::observe(AdoptionRequestObserver::class);
    }
}
