<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount',
        'currency',
        'donor_name',
        'is_monthly',
        'status',
        'payment_method',
    ];
}
