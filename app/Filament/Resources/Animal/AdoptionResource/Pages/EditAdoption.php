<?php

namespace App\Filament\Resources\Animal\AdoptionResource\Pages;

use App\Filament\Resources\Animal\AdoptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdoption extends EditRecord
{
    protected static string $resource = AdoptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
