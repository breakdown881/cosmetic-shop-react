<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory;
    use Searchable;

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
        'created_by',
        'status',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'price' => 'integer',
        'discount_percentage' => 'integer',
        'inventory_qty' => 'integer',
        'star' => 'float',
        'status' => 'integer',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'discount_percentage' => $this->discount_percentage,
            'inventory_qty' => $this->inventory_qty,
            'description' => $this->description,
            'star' => $this->star,
            'featured' => $this->featured,
            'status' => $this->status,
        ];
    }

    public function searchableAs(): string
    {
        return 'product_index';
    }
}
