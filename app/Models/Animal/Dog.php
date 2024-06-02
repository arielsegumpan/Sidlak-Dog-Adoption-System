<?php

namespace App\Models\Animal;

use App\Models\Adoption\AdoptionRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Dog extends Model
{
    use HasFactory;
    protected $table = 'dogs';
    protected $fillable = [
        'name',
        'breed_id',
        'age',
        'gender',
        'size',
        'color',
        'description',
        'image',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

    public function adoption_request() : HasOne
    {
        return $this->hasOne(AdoptionRequest::class);
    }
}
