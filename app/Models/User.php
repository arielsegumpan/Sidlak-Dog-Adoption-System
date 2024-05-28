<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Adoption\AdoptionRequest;
use App\Models\Animal\Dog;
use App\Models\Donation\Donation;
use App\Models\Post\Comment;
use App\Models\Post\Post;
use App\Models\Volunteer\Volunteer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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


    public function dogs() : HasMany
    {
        return $this->hasMany(Dog::class);
    }

    public function adoption_requests() : HasMany
    {
        return $this->hasMany(AdoptionRequest::class);
    }

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function volunteer() : HasOne
    {
        return $this->hasOne(Volunteer::class);
    }

    public function donations() : HasMany
    {
        return $this->hasMany(Donation::class);
    }

}
