<?php

namespace Database\Seeders;

use App\Models\Animal\Breed;
use App\Models\Animal\Dog;
use App\Models\Post\Category;
use App\Models\Post\Comment;
use App\Models\Post\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $breeds = Breed::factory(10)->create();
        $breeds->each(function ($breed) {
            Dog::factory(5)->create(['breed_id' => $breed->id]);
        });

        User::factory(10)->create();
        $categories = Category::factory(30)->create();

        // For each category, create a number of posts
        $categories->each(function ($category) {
            Post::factory(10)->create(['category_id' => $category->id])->each(function ($post) {
                Comment::factory(10)->create(['post_id' => $post->id]);
            });
        });
    }
}
