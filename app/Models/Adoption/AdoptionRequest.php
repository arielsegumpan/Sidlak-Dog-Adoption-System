<?php

namespace App\Models\Adoption;

use App\Models\Animal\Dog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdoptionRequest extends Model
{
    use HasFactory;
    protected $table = 'adoption_requests';
    protected $fillable = [
        'adoption_number',
        'dog_id',
        'user_id',
        'status'
    ];

    // protected $casts = [
    //     'status' => 'boolean'
    // ];

    public function dog() : BelongsTo
    {
        return $this->belongsTo(Dog::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
