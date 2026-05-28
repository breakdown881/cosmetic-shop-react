<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'is_fixed',
        'discount_amount',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'is_fixed' => 'integer',
        'discount_amount' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
