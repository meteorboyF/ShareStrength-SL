<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';
    protected $primaryKey = 'application_id';

    public $timestamps = false; // 'created_at' exists in DB but no updated_at

    protected $fillable = [
        'task_id',
        'helper_id',
        'status',
    ];

    // Relationships

    public function applicant()
    {
        return $this->morphTo(__FUNCTION__, 'applicant_type', 'helper_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}