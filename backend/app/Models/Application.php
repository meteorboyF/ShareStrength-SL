<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'helper_id',
        'status',
    ];

    // Relationships

    public function helper()
    {
        return $this->belongsTo(User::class, 'helper_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}