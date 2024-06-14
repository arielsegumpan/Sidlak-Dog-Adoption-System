<?php

namespace App\Filament\Resources\Animal\AdoptionResource\Pages;

use App\Enums\AdoptionEnum;
use App\Filament\Resources\Animal\AdoptionResource;
use App\Models\Adoption\Adoption;
use App\Models\Animal\Dog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditAdoption extends EditRecord
{
    protected static string $resource = AdoptionResource::class;
    protected $oldDogId;
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

    protected function beforeSave() : void
    {
        $this->oldDogId = $this->record->getOriginal('dog_id');
    }
    protected function afterSave(): void
    {
        $newDogId = $this->record->dog_id;
        $adoptionStatus = $this->record->status;

        if ($this->oldDogId != $newDogId) {
            DB::transaction(function () use ($newDogId, $adoptionStatus) {
                Dog::where('id', $this->oldDogId)->update(['status' => 'available']);

                $dogStatus = $this->determineDogStatus($adoptionStatus);
                Dog::where('id', $newDogId)->update(['status' => $dogStatus]);
            });
        }else{
            DB::transaction(function () use ($newDogId, $adoptionStatus) {
                $dogStatus = $this->determineDogStatus($adoptionStatus);
                Dog::where('id', $newDogId)->update(['status' => $dogStatus]);
            });
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
