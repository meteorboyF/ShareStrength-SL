<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrustedContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trusted_user_id',
        'contact_name',
        'relation', // Added field
        'contact_email',
        'contact_phone',
        'status',
        'verification_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trustedUser()
    {
        return $this->belongsTo(User::class, 'trusted_user_id');
    }
}
