<?php

namespace App\Http\Controllers;

use App\Models\Blog\BlogPost;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $posts = BlogPost::where('is_published', true)
                ->latest('created_at')
                ->take(4)
                ->get(['id', 'post_title', 'post_image', 'post_slug', 'post_content']);

        return view('home', compact('posts'));
    }
}
