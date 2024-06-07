<?php

namespace App\Filament\Resources\Animal\AdoptionResource\Pages;

use App\Filament\Resources\Animal\AdoptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdoptions extends ListRecords
{
    protected static string $resource = AdoptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-m-plus'),
        ];
    }


}
