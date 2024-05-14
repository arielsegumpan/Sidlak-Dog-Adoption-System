<?php

namespace App\Observers;
use App\Models\AdoptionRequest;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

class AdoptionRequestObserver
{
    /**
     * Handle the AdoptionRequest "created" event.
     */
    public function created(AdoptionRequest $adoptionRequest): void
    {
        // $dog = $adoptionRequest->dog;

        // /** @var User $user */
        // $user = auth()->user();
        // // Check if the dog exists and is already adopted
        // if ($dog && $dog->is_adopted) {
        //     Notification::make()
        //     ->title('Someone wants to adopt :' . $adoptionRequest->dog?->dog_name)
        //     ->sendToDatabase($adoptionRequest->$user);

        // }


    }


    /**
     * Handle the AdoptionRequest "updated" event.
     */
    public function updated(AdoptionRequest $adoptionRequest): void
    {
        // $dog = $adoptionRequest->dog;
        // if ($dog->is_adopted) {
        //    return;
        // }

        if ($adoptionRequest->isDirty('is_approved')) {

            if($adoptionRequest->dog?->is_adopted){
                Notification::make()
                ->warning()
                ->title('Not available')
                ->body('The selected dog is already adopted.')
                ->color('danger')
                ->send();

                $this->halt();

            }
            // $adoptionRequest->dog()->update(['is_adopted' => $adoptionRequest->is_approved]);
        }
    }

    /**
     * Handle the AdoptionRequest "deleted" event.
     */
    public function deleted(AdoptionRequest $adoptionRequest): void
    {
        //
    }

    /**
     * Handle the AdoptionRequest "restored" event.
     */
    public function restored(AdoptionRequest $adoptionRequest): void
    {
        //
    }

    /**
     * Handle the AdoptionRequest "force deleted" event.
     */
    public function forceDeleted(AdoptionRequest $adoptionRequest): void
    {
        //
    }
}
