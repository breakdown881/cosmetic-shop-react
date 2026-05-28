<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'email',
        'fullname',
        'star',
        'description',
        'active',
    ];

    protected $casts = [
        'star' => 'float',
        'active' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
