<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'phone_number',
        'address',
        'user_type',
        'profile_photo',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_hash' => 'hashed',
        ];
    }

    // Relationships

    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_id', 'user_id');
    }

    public function trustedContacts()
    {
        return $this->hasMany(TrustedContact::class, 'user_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'user_id');
    }

    public function resources()
    {
        return $this->hasMany(Resource::class, 'uploaded_by', 'user_id');
    }
}