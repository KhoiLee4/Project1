<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
                'phone_number' => $this->user->phone_number ?? null,
                'avatar' => $this->user->avatar ? [
                    'id' => $this->user->avatar->id,
                    'image_url' => $this->user->avatar->image_url,
                    'full_url' => $this->user->avatar->full_url,
                ] : null,
            ],
            'venue' => [
                'id' => $this->venue->id ?? null,
                'name' => $this->venue->name ?? null,
            ],
            'venue_id' => $this->venue_id,
            'star_number' => $this->star_number,
            'review' => $this->review,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
