<?php

namespace App\Filament\Resources\AdoptionRequestResource\Pages;

use App\Filament\Resources\AdoptionRequestResource;
use App\Models\AdoptionRequest;
use Filament\Actions;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAdoptionRequest extends CreateRecord
{
    protected static string $resource = AdoptionRequestResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }



    protected function beforeCreate(): void
    {
        // $adoption = AdoptionRequest::with('dog:id,is_adopted,dog_name')->get();
        // $isAdoptedValues = $adoption->pluck('dog.is_adopted');


        // if ($isAdoptedValues->contains(1)) {

        //     Notification::make()
        //         ->warning()
        //         ->title('Not available')
        //         ->body('The selected dog is already adopted.')
        //         ->color('danger')
        //         ->send();
        //     $this->halt();
        // }
    }
}
