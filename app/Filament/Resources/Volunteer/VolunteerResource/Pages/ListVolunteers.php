<?php

namespace App\Filament\Resources\Volunteer\VolunteerResource\Pages;

use App\Filament\Resources\Volunteer\VolunteerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVolunteers extends ListRecords
{
    protected static string $resource = VolunteerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
