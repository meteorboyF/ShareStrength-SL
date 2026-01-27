<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'category',
        'recipient',
        'description',
        'amount',
        'date',
    ];
}
