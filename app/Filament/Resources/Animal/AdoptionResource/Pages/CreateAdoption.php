<?php

namespace App\Filament\Resources\Animal\AdoptionResource\Pages;

use App\Filament\Resources\Animal\AdoptionResource;
use App\Filament\Resources\Animal\DogResource;
use App\Models\Animal\Dog;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdoption extends CreateRecord
{
    protected static string $resource = AdoptionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function afterCreate(): void
    {
        $dogId = $this->record->dog_id;
        $adoptionStatus = $this->form->getState()['status'];
        if ($dogId && $adoptionStatus === "approved") {
            Dog::query()->where('id', $dogId)->update(['status' => 'adopted']);
        }
    }
}
