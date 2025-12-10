<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';
    protected $primaryKey = 'application_id';

    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'helper_id',
        'status',
    ];

    // Relationships

    public function helper()
    {
        return $this->belongsTo(Helper::class, 'helper_id', 'helper_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }
}