<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $primaryKey = 'task_id';
    
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'skill_required',
        'hourly_rate',
        'urgency',
        'status',
    ];

    protected $casts = [
        'required_skills' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function caregiver()
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }

    public function hiring_decision()
    {
        return $this->hasOne(HiringDecision::class, 'task_id');
    }
    
    // Virtual attribute for caregiver via hiring decision
    public function getCaregiverAttribute()
    {
        return $this->hiring_decision && $this->hiring_decision->decision_status === 'approved' 
            ? $this->hiring_decision->helper 
            : null;
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