<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceListDetail extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'date',
        'day',
        'start_time',
        'end_time',
        'price',
        'fixed_price',
        'current_price',
        'price_list_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'date' => 'date',
            'price' => 'decimal:2',
            'fixed_price' => 'decimal:2',
            'current_price' => 'decimal:2',
        ];
    }

    public function priceList()
    {
        return $this->belongsTo(PriceList::class, 'price_list_id');
    }
}
