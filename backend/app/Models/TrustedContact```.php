<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrustedContact extends Model
{
    use HasFactory;

    protected $table = 'trusted_contacts';
    protected $primaryKey = 'contact_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'relationship',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}