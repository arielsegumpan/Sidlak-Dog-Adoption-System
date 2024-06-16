<?php

namespace App\Livewire\Animal;

use App\Models\Animal\Breed;
use App\Models\Animal\Dog;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class DogList extends Component
{
    use WithPagination;

    public $breeds;

    public function mount()
    {
        // Fetch all breeds
        $this->breeds = Breed::get(['id','breed_name', 'breed_slug']);
    }

    #[Computed()]
    public function dogs()
    {
        // return Dog::with(['breed:id,breed_name'])->where('status', 'available')->paginate(6);
        return Dog::with(['breed:id,breed_name'])->where('status', 'available')->simplePaginate(6)->through(function ($dog) {
            // Check if dog_image is already an array
            $dogImages = is_array($dog->dog_image) ? $dog->dog_image : json_decode($dog->dog_image, true);
            // Get the first image URL
            $dog->first_image = isset($dogImages[0]['dog_image']) ? $dogImages[0]['dog_image'] : 'default.jpg';
            return $dog;
        });
    }

    public function render()
    {
        return view('livewire.animal.dog-list', [
            'dogs' => $this->dogs(),
            'breeds' => $this->breeds
        ]);
    }
}
