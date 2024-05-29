<?php

namespace Database\Factories\Post;

use App\Models\Post\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $title = $this->faker->unique()->sentence(4),
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->unique()->sentence(8),
            'body' => $this->faker->realText(),
            'image' => $this->faker->imageUrl(640, 480, 'animals', true),
            'is_featured' => $this->faker->boolean(),
            'is_published' => $this->faker->boolean(),
            'category_id' => Category::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => $this->faker->dateTimeBetween('-5 month', 'now'),
        ];
    }
}
