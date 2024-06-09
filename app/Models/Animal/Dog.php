<?php

namespace App\Models\Animal;

use App\Models\Adoption\Adoption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Dog extends Model
{
    use HasFactory;
    protected $table = 'dogs';
    protected $fillable = [
        'dog_name',
        'dog_age',
        'breed_id',
        'dog_size',
        'dog_gender',
        'dog_description',
        'dog_image',
        'status',
    ];

    protected $casts = [
        'dog_image' => 'array',
    ];

    public function getFirstDogImageAttribute()
    {
        $images = $this->dog_image;
        return $images[0] ?? null;
    }



    public function breed() : BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function adoption() : HasOne
    {
        return $this->hasOne(Adoption::class);
    }

    public function medicalRecords() : HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'dog_id', 'id');
    }

    // public function fosters()
    // {
    //     return $this->hasMany(Foster::class);
    // }

    // public function testimonials()
    // {
    //     return $this->hasMany(Testimonial::class);
    // }


}
