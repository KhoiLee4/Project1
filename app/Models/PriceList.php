<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'venue_id',
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

    public function details()
    {
        return $this->hasMany(PriceListDetail::class, 'price_list_id');
    }
}
