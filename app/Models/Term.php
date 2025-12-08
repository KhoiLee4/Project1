<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'update_time',
        'term_category',
        'content',
        'venue_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'update_time' => 'datetime',
            'term_category' => 'integer',
        ];
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}
