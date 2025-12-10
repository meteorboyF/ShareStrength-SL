<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $table = 'resources';
    protected $primaryKey = 'resource_id';

    // Your table has both created_at and updated_at.
    public $timestamps = true;

    protected $fillable = [
        'name',
        'category',
        'description',
        'link',
        'uploaded_by',
        'approved_by',
    ];
}