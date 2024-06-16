<?php

namespace App\Livewire\Blog;

use App\Models\Blog\BlogPost;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class BlogPostList extends Component
{
    use WithPagination;
    #[Computed()]
    public function posts(){
        return BlogPost::with([
            'author:id,name,profile_photo_path',
            'categories:id,category_name'
        ])
        ->where('is_published', true)
        ->latest('created_at')
        ->select('id', 'post_title', 'post_image', 'post_slug', 'post_content', 'author_id', 'created_at')  // Specify the necessary columns
        ->paginate(6);
    }

    public function render()
    {
        return view('livewire.blog.blog-post-list');
    }
}
