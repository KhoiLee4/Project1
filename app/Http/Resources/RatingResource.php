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
            ],
            'venue_id' => $this->venue_id,
            'star_number' => $this->star_number,
            'review' => $this->review,
            'created_at' => $this->created_at,
        ];
    }
}
