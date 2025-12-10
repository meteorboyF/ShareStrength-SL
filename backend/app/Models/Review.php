<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    protected $primaryKey = 'review_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'helper_id',
        'hiring_id',
        'comment',
        'rating',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function helper()
    {
        return $this->belongsTo(Helper::class, 'helper_id', 'helper_id');
    }
}