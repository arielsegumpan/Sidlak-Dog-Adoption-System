<?php

namespace App\Filament\Resources\Animal\DogResource\Pages;

use App\Filament\Resources\Animal\DogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDog extends ViewRecord
{
    protected static string $resource = DogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['dog_gender'] = ucfirst($data['dog_gender']);
        $data['dog_size'] = ucfirst($data['dog_size']);

        return $data;
    }

}
