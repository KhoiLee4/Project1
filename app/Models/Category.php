<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }

    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'venues_categories', 'category_id', 'venue_id')
                    ->withTimestamps();
    }

    public function grounds()
    {
        return $this->hasMany(Ground::class, 'category_id');
    }
}
