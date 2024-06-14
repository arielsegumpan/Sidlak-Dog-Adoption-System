<?php

namespace Database\Factories\Animal;

use App\Models\Animal\Dog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{

    /**
     * Veterinarian names.
     *
     * @var array
     */
    protected $veterinarians = [
        'Dr. Samantha Smith',
        'Dr. Michael Johnson',
        'Dr. Emily Brown',
        'Dr. Daniel Martinez',
        'Dr. Sarah Davis',
        'Dr. Christopher Wilson',
        'Dr. Jennifer Taylor',
        'Dr. David Anderson',
        'Dr. Jessica Garcia',
        'Dr. Matthew Thomas',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dog_id' => Dog::inRandomOrder()->first()->id,
            'record_date' => $this->faker->date(),
            'type' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'veterinarian' => $this->faker->randomElement($this->veterinarians),
        ];
    }
}
