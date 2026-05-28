<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model implements HasMedia
{
    use HasFactory;
    use Searchable;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'created_by',
        'status',
    ];

    public function toSearchableArray(): array
    {
        return [
            'name'          => $this->name,
            'created_by'    => $this->created_by,
            'status'        => $this->status,
        ];
    }

    public function searchableAs(): string
    {
        return 'brand_index';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('brands')->singleFile();
    }
}
