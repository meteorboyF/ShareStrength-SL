<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'caregiver_id',
        'title',
        'description',
        'location',
        'budget',
        'status',
        'required_skills',
        'urgency',
        'scheduled_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'required_skills' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function caregiver()
    {
        return $this->belongsTo(Helper::class, 'caregiver_id');
    }

    public function hiring_decision()
    {
        return $this->hasOne(HiringDecision::class, 'task_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function resource()
    {
        return $this->hasOne(Resource::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
