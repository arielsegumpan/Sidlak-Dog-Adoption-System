<?php

namespace Database\Seeders;

use App\Models\Animal\Breed;
use Illuminate\Support\Str;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $dogBreeds = [
            'Aspin',
            'Labrador Retriever',
            'German Shepherd',
            'Golden Retriever',
            'Beagle',
            'Siberian Husky',
        ];

        $breedImages = [
            'Aspin' => 'aspin.jpg',
            'Labrador Retriever' => 'labrador_retriever.jpg',
            'German Shepherd' => 'german_shepherd.jpg',
            'Golden Retriever' => 'golden_retriever.jpg',
            'Beagle' => 'beagle.jpg',
            'Siberian Husky' => 'siberian_husky.jpg',
        ];

        foreach ($dogBreeds as $breedName) {
            Breed::factory()->create([
                'breed_name' => $breedName,
                'breed_slug' => Str::slug($breedName),
                'breed_image' => $breedImages[$breedName],
            ]);
        }

    }
}
