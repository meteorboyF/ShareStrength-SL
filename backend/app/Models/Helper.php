<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Helper extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'helpers';
    protected $primaryKey = 'helper_id';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'phone_number',
        'address',
        'skills',
        'rating',
        'profile_photo',
        'verification_status',
        'status',
        'verified_by',
    ];

    protected $hidden = [
        'password_hash',
    ];

    // Password accessor for Laravel Auth
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Relationships
    
    // Applications made by this helper
    public function applications()
    {
        return $this->hasMany(Application::class, 'helper_id');
    }

    // Hiring records for this helper
    public function hiringRecords()
    {
        return $this->hasMany(HiringRecord::class, 'helper_id');
    }

    // Reviews received by this helper
    public function reviews()
    {
        return $this->hasMany(Review::class, 'helper_id');
    }
}
