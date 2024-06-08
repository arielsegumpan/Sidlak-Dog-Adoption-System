<?php

namespace App\Filament\Resources\Animal\DogResource\Pages;

use App\Filament\Resources\Animal\DogResource;
use App\Models\Adoption\Adoption;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDog extends CreateRecord
{
    protected static string $resource = DogResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $dogId = $this->record->dog_id;
        if ($dogId) {
            Adoption::query()->where('id', $dogId)->update(['status' => 'pending']);
        }
    }
}
