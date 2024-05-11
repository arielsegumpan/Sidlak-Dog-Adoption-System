<?php

namespace App\Models\Animal;

use App\Models\AdoptionRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dog extends Model
{
    use HasFactory;
    protected $table = 'dogs';
    protected $guarded = ['id','created_at','updated_at'];

    public function breed() : BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    protected function AdoptRequests() : HasMany
    {
        return $this->hasMany(AdoptionRequest::class, 'dog_id');
    }
}
