<?php

namespace App\Filament\Resources\Animal\DogResource\Pages;

use App\Enums\DogEnum;
use App\Filament\Resources\Animal\DogResource;
use App\Models\Adoption\Adoption;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDog extends EditRecord
{
    protected static string $resource = DogResource::class;

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


    protected function afterSave(): void
    {
        $dogId = $this->record->id;
        $adoptionStatus = $this->record->status;

        if ($dogId) {
            $dogAdoptionStatus = $this->determineAdoptionStatus($adoptionStatus);
            Adoption::query()->where('id', $dogId)->update(['status' => $dogAdoptionStatus]);
        }
    }

    /**
     * Determine the dog's status based on the adoption status.
     *
     * @param string $adoptionStatus
     * @return string
     */
    protected function determineAdoptionStatus(string $adoptionStatus): string
    {
        switch ($adoptionStatus) {
            case DogEnum::Available->value:
                return 'pending';
            case DogEnum::Adopted->value:
            case DogEnum::Foster->value:
            default:
                return 'approved';
        }
    }
}
