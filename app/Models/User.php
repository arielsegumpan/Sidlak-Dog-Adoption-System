<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Adoption\Adoption;
use App\Models\Blog\BlogPost;
use App\Models\Blog\Comment;
use App\Models\Donation\Donation;
use App\Models\Volunteer\Volunteer;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;


    const ROLE_ADMIN = 0;
    const ROLE_ACCOUNTING = 1;
    const ROLE_MANAGER = 2;
    const ROLE_EDITOR = 3;
    const ROLE_USER = 4;
    const ROLE_DEFAULT = self::ROLE_USER;
    const ROLES = [
        self::ROLE_ADMIN => 'Admin', //overall access
        self::ROLE_ACCOUNTING => 'Accounting', //manage donations
        self::ROLE_MANAGER => 'Manager', //manage dogs and blogs as well as users
        self::ROLE_EDITOR => 'Editor', //manage dogs and blogs
        self::ROLE_USER => 'User', //view dogs, blogs, comments, adoptions and donations
    ];

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Unknown';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() || $this->isAccounting() || $this->isManager() || $this->isEditor();
    }


    public function isAdmin(){
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAccounting(){
        return $this->role === self::ROLE_ACCOUNTING;
    }

    public function isManager(){
        return $this->role === self::ROLE_MANAGER;
    }

    public function isEditor(){
        return $this->role === self::ROLE_EDITOR;
    }

    public function isUser(){
        return $this->role === self::ROLE_USER;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
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
