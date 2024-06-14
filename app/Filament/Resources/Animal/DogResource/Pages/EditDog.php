<?php

namespace App\Filament\Resources\Animal\DogResource\Pages;

use App\Enums\DogEnum;
use App\Filament\Resources\Animal\DogResource;
use App\Models\Adoption\Adoption;
use Filament\Actions;
use Filament\Notifications\Notification;
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

    protected function beforeSave(): void
    {
        $dogId = $this->getRecord()->id; //kwa id sang ido
        $adoptionStatus = $this->form->getState()['status']; //kwa state sang status select form
        $dogExists = Adoption::query()->where('dog_id', $dogId)->exists(); // check kung and id ga exists sa adoption table
        $dogAdoptionStatus = $this->determineAdoptionStatus($adoptionStatus); // check kung ano nga status para sa adoption

        if (!$dogExists && $adoptionStatus != "available" && $dogAdoptionStatus != "pending") {
            Notification::make()
                ->title('Cannot change adoption status.')
                ->body('Adopter of ' . $this->getRecord()->dog_name . ' is not exists. ')
                ->icon('heroicon-o-x-circle')
                ->iconColor('danger')
                ->color('danger')
                ->send();
            $this->redirect(DogResource::getUrl('index'));
            $this->halt();

        }

        Adoption::query()->where('dog_id', $dogId)->update(['status' => $dogAdoptionStatus]);
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
