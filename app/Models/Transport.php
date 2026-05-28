<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'price',
    ];

    protected $casts = [
        'province_id' => 'integer',
        'price' => 'integer',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
