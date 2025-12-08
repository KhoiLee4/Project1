<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'booking_id',
        'amount',
        'unit_price',
        'method',
        'note',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'amount' => 'decimal:2',
            'unit_price' => 'decimal:2',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
