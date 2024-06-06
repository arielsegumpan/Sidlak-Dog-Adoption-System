<?php

namespace App\Models\Donation;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $table = 'donations';
    protected $fillable = [
        'user_id', 'amount', 'donation_type', 'donation_message', 'is_verified', 'donation_date'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
