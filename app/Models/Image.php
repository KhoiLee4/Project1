<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }

    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'image_venus', 'image_id', 'venue_id')
                    ->withPivot('is_image')
                    ->withTimestamps();
    }

    // Accessor to get full URL
    public function getFullUrlAttribute()
    {
        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }
        
        return asset($this->image_url);
    }
}
