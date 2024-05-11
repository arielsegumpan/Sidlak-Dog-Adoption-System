<?php

namespace App\Models\Animal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dog extends Model
{
    use HasFactory;
    protected $table = 'dogs';
    protected $guarded = ['id','created_at','updated_at'];

    public function breed() : BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

}
