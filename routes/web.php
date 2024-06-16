<?php

use App\Http\Controllers\Animal\DogController;
use App\Http\Controllers\Blog\BlogPostController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::get('/blog_post', [BlogPostController::class, 'index'])->name('posts.index');
Route::get('/dogs', [DogController::class, 'index'])->name('dogs.index');
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');
});
