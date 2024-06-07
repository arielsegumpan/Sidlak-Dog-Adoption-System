<?php

namespace App\Filament\Resources\Animal\AdoptionResource\Pages;

use App\Filament\Resources\Animal\AdoptionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdoption extends CreateRecord
{
    protected static string $resource = AdoptionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
