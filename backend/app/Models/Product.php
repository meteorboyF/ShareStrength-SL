<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';

    // Your table has both created_at and updated_at.
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'stock_quantity',
        'category',
        'vendor',
    ];
}