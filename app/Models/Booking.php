<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'amount_time',
        'is_event',
        'ground_id',
        'target',
        'customer_note',
        'owner_note',
        'quantity',
        'status',
        'event_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'date' => 'date',
            'amount_time' => 'integer',
            'is_event' => 'boolean',
            'quantity' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ground()
    {
        return $this->belongsTo(Ground::class, 'ground_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }
}
