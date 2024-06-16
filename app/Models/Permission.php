<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role;

class Permission extends SpatiePermission
{
    use HasFactory;


     /**
     * Get the roles that have the permission.
     */
    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the users that have the permission.
     */
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
