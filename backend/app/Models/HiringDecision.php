<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiringDecision extends Model
{
    use HasFactory;

    protected $table = 'hiring_decisions';
    protected $primaryKey = 'decision_id';
    public $timestamps = false; // 'created_at' exists but 'updated_at' is missing in schema dump? dump says created_at timestamp default current. No updated_at.

    protected $fillable = [
        'task_id',
        'application_id',
        'selected_helper_id',
        'decision_status',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function helper()
    {
        return $this->belongsTo(Helper::class, 'selected_helper_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}
