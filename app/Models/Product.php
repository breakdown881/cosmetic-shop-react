<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'brand_id',
        'category_id',
        'price',
        'discount_percentage',
        'discount_from_date',
        'discount_to_date',
        'media_id',
        'inventory_qty',
        'description',
        'star',
        'featured',
    ];
}
