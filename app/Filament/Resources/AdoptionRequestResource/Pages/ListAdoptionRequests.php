<?php

namespace App\Filament\Resources\AdoptionRequestResource\Pages;

use App\Filament\Resources\AdoptionRequestResource;
use App\Models\AdoptionRequest;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAdoptionRequests extends ListRecords
{
    protected static string $resource = AdoptionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-m-plus')->label('Adoption Request')
        ];
    }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['is_approved'] = 1;
    //     return $data;
    // }
}
