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
        $posts = Post::factory(1000)->create();
        $posts->each(function ($post) use ($categories) {
            $post->categories()->attach(
                $categories->random(5)->pluck('id')->toArray()
            );

            Comment::factory(100)->create(['post_id' => $post->id]);
        });
    }
}
