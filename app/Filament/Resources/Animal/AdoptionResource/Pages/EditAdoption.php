<?php

namespace App\Filament\Resources\Animal\AdoptionResource\Pages;

use App\Enums\AdoptionEnum;
use App\Filament\Resources\Animal\AdoptionResource;
use App\Models\Adoption\Adoption;
use App\Models\Animal\Dog;
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

    protected function afterSave(): void
    {
        $dogId = $this->record->dog_id;
        $adoptionStatus = $this->record->status;

        if ($dogId) {
            $dogStatus = $this->determineDogStatus($adoptionStatus);
            Dog::query()->where('id', $dogId)->update(['status' => $dogStatus]);
            // if (in_array($adoptionStatus, [AdoptionEnum::PENDING->value, AdoptionEnum::REJECTED->value])) {
            //     Adoption::query()->where('id', $dogId)->update([
            //         'status' => $dogStatus,
            //         'user_id' => null
            //     ]);


            // }

        }

    }

    /**
     * Determine the dog's status based on the adoption status.
     *
     * @param string $adoptionStatus
     * @return string
     */
    protected function determineDogStatus(string $adoptionStatus): string
    {
        switch ($adoptionStatus) {
            case AdoptionEnum::APPROVED->value:
                return 'adopted';
            case AdoptionEnum::PENDING->value:
            case AdoptionEnum::REJECTED->value:
            default:
                return 'available';
        }
    }
}
