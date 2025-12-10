<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    protected $primaryKey = 'task_id';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'description',
        'skill_required',
        'hourly_rate',
        'urgency',
        'user_id',
        'status',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'task_id', 'task_id');
    }
}