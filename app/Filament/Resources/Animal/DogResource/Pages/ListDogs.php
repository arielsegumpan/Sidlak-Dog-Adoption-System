<?php

namespace App\Filament\Resources\Animal\DogResource\Pages;

use App\Filament\Resources\Animal\DogResource;
use App\Models\Animal\Dog;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListDogs extends ListRecords
{
    protected static string $resource = DogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-m-plus')->label('Dog')
            ->before(function (CreateAction $action, Dog $record) {
                if ($record->is_adopted) {
                    Notification::make()
                        ->warning()
                        ->title(ucfirst($record->dog_name) . ' is already adopted.')
                        ->persistent()
                        ->send();
                    $action->halt();
                }
            })
            ,
        ];
    }
}
