<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'amount',
        'currency',
        'donor_name',
        'is_monthly',
        'status',
        'payment_method',
    ];
}
