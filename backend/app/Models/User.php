<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'user_type', // disabled_individual, family_member, caretaker
        'phone_number',
        'address',
        'profile_photo',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    // Password accessor for Laravel Auth
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Relationships
    public function tasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'caregiver_id');
    }

    public function trustedContacts()
    {
        return $this->hasMany(TrustedContact::class, 'user_id');
    }
}