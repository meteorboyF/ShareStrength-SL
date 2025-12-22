<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // Tell Laravel which table to look at
    protected $table = 'tasks';

    // Tell Laravel the name of your ID column
    protected $primaryKey = 'task_id';

    // Tell Laravel which columns are okay to fill with data
    protected $fillable = ['title', 'description', 'skill_required', 'hourly_rate', 'urgency', 'status'];
}