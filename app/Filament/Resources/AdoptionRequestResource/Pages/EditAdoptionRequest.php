<?php

namespace App\Filament\Resources\AdoptionRequestResource\Pages;

use App\Filament\Resources\AdoptionRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdoptionRequest extends EditRecord
{
    protected static string $resource = AdoptionRequestResource::class;

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


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_approved'] = 1;

        return $data;
    }
}
