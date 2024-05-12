<?php

namespace App\Observers;

use App\Models\AdoptionRequest;
use Filament\Notifications\Notification;

class AdoptionRequestObserver
{
    public function created(AdoptionRequest $adoptionRequest): void
    {
        Notification::make()
        ->title('Adoption Request by :' . $adoptionRequest->name)
        ->sendToDatabase($adoptionRequest->user);
    }
}
