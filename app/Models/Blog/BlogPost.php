<?php

namespace App\Models\Blog;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    use HasFactory;

    protected $table = 'blog_posts';
    protected $fillable = [
        'post_title', 'post_slug', 'post_content', 'post_image', 'author_id', 'is_published'
    ];

    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class,'post_id','id');
    }

    // public function tags()
    // {
    //     return $this->belongsToMany(Tag::class, 'blog_post_tag');
    // }

    public function categories() : BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'blog_post_category')->withTimestamps();
    }
}
