<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceListDetail extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'wholesale',
        'unit_wholesale',
        'retail',
        'unit_retail',
        'service_list_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }

    public function serviceList()
    {
        return $this->belongsTo(ServiceList::class, 'service_list_id');
    }
}
