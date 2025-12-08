<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'phone_number',
        'email',
        'name',
        'password',
        'gender',
        'birthday',
        'role',
        'is_admin',
        'is_active',
        'avatar_id',
        'cover_image_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender' => 'boolean',
            'birthday' => 'datetime',
            'role' => 'boolean',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function avatar()
    {
        return $this->belongsTo(Image::class, 'avatar_id');
    }

    public function coverImage()
    {
        return $this->belongsTo(Image::class, 'cover_image_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    public function ownedVenues()
    {
        return $this->hasMany(Venue::class, 'owner_id');
    }

}
