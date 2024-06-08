<?php

namespace App\Filament\Resources\Animal\MedicalRecordResource\Pages;

use App\Filament\Resources\Animal\MedicalRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalRecord extends CreateRecord
{
    protected static string $resource = MedicalRecordResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
