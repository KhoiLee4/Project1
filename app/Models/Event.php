<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'price',
        'ticket_number',
        'level',
        'venue_id',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'price' => 'decimal:2',
            'ticket_number' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'event_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}
