<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ground extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'venue_id',
        'category_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'ground_id');
    }
}
