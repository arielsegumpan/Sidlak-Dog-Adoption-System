<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
