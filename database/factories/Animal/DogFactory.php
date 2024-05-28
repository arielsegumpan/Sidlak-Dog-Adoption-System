<?php

namespace Database\Factories\Animal;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Animal\Dog>
 */
class DogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'breed_id' => \App\Models\Animal\Breed::factory(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'age' => $this->faker->numberBetween(0, 10),
            'size' => $this->faker->randomElement(['Small', 'Medium', 'Large']),
            'color' => $this->faker->colorName,
            'description' => $this->faker->sentence(120),
            'image' => $this->faker->imageUrl(640, 480, 'animals', true),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
