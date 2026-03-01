<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // Add this line

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'caregiver_id',
        'title',
        'description',
        'location',
        'latitude',
        'longitude',
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

    /**
     * Accessor to ensure `required_skills` is always a clean array,
     * even if the database contains corrupted or double-encoded JSON.
     * 
     * This runs BEFORE the `$casts` property, preventing decoding errors.
     */
    protected function requiredSkills(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value) || empty($value) || $value === '[null]') {
                    return [];
                }

                // First decode attempt
                $skills = json_decode($value, true);

                // If it's still a string, it was double-encoded. Decode again.
                if (is_string($skills)) {
                    $skills = json_decode($skills, true);
                }

                return is_array($skills) ? $skills : [];
            }
        );
    }

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