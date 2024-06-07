<?php

namespace App\Filament\Resources\Volunteer\VolunteerResource\Pages;

use App\Filament\Resources\Volunteer\VolunteerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVolunteer extends CreateRecord
{
    protected static string $resource = VolunteerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
