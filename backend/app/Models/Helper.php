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

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'location',
        'skills',
        'bio',
        'profile_photo',
        'profile_photo_url',
        'is_verified',
        'is_active',
        'rating',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:1',
    ];

    // Applications made by this helper
    public function applications()
    {
        return $this->hasMany(Application::class, 'helper_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'caregiver_id');
    }

    // Reviews received by this helper
    public function reviews()
    {
        return $this->hasMany(Review::class, 'helper_id');
    }
}
