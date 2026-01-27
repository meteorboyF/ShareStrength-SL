<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    protected $fillable = [
        'task_id',
        'helper_id',
        'applicant_type',
        'status',
    ];

    // Relationships

    public function helper()
    {
        return $this->belongsTo(Helper::class, 'helper_id');
    }

    public function applicant()
    {
        return $this->belongsTo(Helper::class, 'helper_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
