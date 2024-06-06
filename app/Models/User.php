<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Adoption\Adoption;
use App\Models\Blog\BlogPost;
use App\Models\Blog\Comment;
use App\Models\Donation\Donation;
use App\Models\Volunteer\Volunteer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function adoptions() : HasMany
    {
        return $this->hasMany(Adoption::class);
    }

    // public function fosters()
    // {
    //     return $this->hasMany(Foster::class);
    // }

    public function donations() : HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function volunteers() : HasMany
    {
        return $this->hasMany(Volunteer::class);
    }


    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
