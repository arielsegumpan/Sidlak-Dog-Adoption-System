<?php

namespace App\Models\Animal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    use HasFactory;
    protected $table = 'medical_records';
    protected $fillable = [
        'dog_id', 'record_date', 'type', 'description',
        'veterinarian',
    ];

    public function dog() : BelongsTo
    {
        return $this->belongsTo(Dog::class,'dog_id', 'id');
    }
}
