<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sub_address' => $this->sub_address,
            'district' => $this->district,
            'city' => $this->city,
            'address' => $this->address,
            'operating_time' => $this->operating_time,
            'phone_number1' => $this->phone_number1,
            'phone_number2' => $this->phone_number2,
            'website' => $this->website,
            'deposit' => $this->deposit,
            'owner' => [
                'id' => $this->owner->id ?? null,
                'name' => $this->owner->name ?? null,
            ],
            'categories' => $this->categories->map(fn($category) => [
                'id' => $category->id,
                'name' => $category->name,
            ]),
            'images' => $this->images->map(fn($image) => [
                'id' => $image->id,
                'name' => $image->name,
                'image_url' => $image->image_url,
                'full_url' => $image->full_url,
                'is_image' => $image->pivot->is_image ?? true,
            ]),
            'grounds' => GroundResource::collection($this->whenLoaded('grounds')),
            'prices' => PriceResource::collection($this->whenLoaded('prices')),
            'service_lists' => ServiceListResource::collection($this->whenLoaded('serviceLists')),
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
            'average_rating' => $this->ratings->avg('star_number') ?? 0,
            'total_ratings' => $this->ratings->count(),
        ];
    }
}
