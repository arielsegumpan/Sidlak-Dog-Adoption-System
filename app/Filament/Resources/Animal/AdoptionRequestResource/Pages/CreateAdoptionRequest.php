<?php

namespace App\Filament\Resources\Animal\AdoptionRequestResource\Pages;

use App\Filament\Resources\Animal\AdoptionRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdoptionRequest extends CreateRecord
{
    protected static string $resource = AdoptionRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
