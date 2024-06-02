<?php

namespace App\Filament\Resources\Animal\AdoptionRequestResource\Pages;

use App\Filament\Resources\Animal\AdoptionRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdoptionRequests extends ListRecords
{
    protected static string $resource = AdoptionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-m-plus')->label('Create Request'),
        ];
    }
}
