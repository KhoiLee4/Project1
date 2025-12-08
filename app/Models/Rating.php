<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'venue_id',
        'star_number',
        'review',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'string',
            'venue_id' => 'string',
            'star_number' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}
