<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'prices';

    protected $fillable = [
        'id',
        'date',
        'day',
        'start_time',
        'end_time',
        'fixed_price',
        'current_price',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'fixed_price' => 'decimal:2',
            'current_price' => 'decimal:2',
        ];
    }

    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'venues_categories', 'price_id', 'venue_id')
                    ->withPivot('category_id')
                    ->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'venues_categories', 'price_id', 'category_id')
                    ->withPivot('venue_id')
                    ->withTimestamps();
    }
}

