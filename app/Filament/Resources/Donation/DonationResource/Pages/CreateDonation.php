<?php

namespace App\Filament\Resources\Donation\DonationResource\Pages;

use App\Filament\Resources\Donation\DonationResource;
use App\Models\User;
// use Filament\Actions\Action;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDonation extends CreateRecord
{
    protected static string $resource = DonationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function afterCreate(): void
    {

        $user = auth()->user();

        Notification::make()
            ->title('Saved successfully')
            ->sendToDatabase($user);

        // /** @var Donation $donation */
        // $donation = $this->record;

        // /** @var User $user */
        // $user = auth()->user();

        // Notification::make()
        //     ->title('New donation')
        //     ->icon('heroicon-o-gift')
        //     ->body("**{$donation->user?->name} donated {$donation->amount} pesos.**")
        //     ->actions([
        //         Action::make('View')
        //             ->url(DonationResource::getUrl('edit', ['record' => $donation])),
        //     ])
        //     ->sendToDatabase($user);
    }

}
