<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Helper extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'helpers';
    protected $primaryKey = 'helper_id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'phone_number',
        'address',
        'skills',
        'profile_photo',
        'verification_status',
        'status',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
        ];
    }

    // Relationships

    public function applications()
    {
        return $this->hasMany(Application::class, 'helper_id', 'helper_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'helper_id', 'helper_id');
    }
}