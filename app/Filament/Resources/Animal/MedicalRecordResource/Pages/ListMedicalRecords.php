<?php

namespace App\Filament\Resources\Animal\MedicalRecordResource\Pages;

use App\Filament\Resources\Animal\MedicalRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMedicalRecords extends ListRecords
{
    protected static string $resource = MedicalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-m-plus'),
        ];
    }
}
