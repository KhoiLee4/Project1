<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'venue_id' => $this->venue_id,
            'details' => PriceListDetailResource::collection($this->whenLoaded('details')),
        ];
    }
}
