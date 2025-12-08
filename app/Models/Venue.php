<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'sub_address',
        'district',
        'city',
        'address',
        'operating_time',
        'phone_number1',
        'phone_number2',
        'website',
        'deposit',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'deposit' => 'decimal:2',
        ];
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'image_venus', 'venue_id', 'image_id')
                    ->withPivot('is_image')
                    ->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'venues_categories', 'venue_id', 'category_id')
                    ->withTimestamps();
    }

    public function priceLists()
    {
        return $this->hasMany(PriceList::class, 'venue_id');
    }

    public function serviceLists()
    {
        return $this->hasMany(ServiceList::class, 'venue_id');
    }

    public function terms()
    {
        return $this->hasMany(Term::class, 'venue_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'venue_id');
    }

    public function grounds()
    {
        return $this->hasMany(Ground::class, 'venue_id');
    }
}
