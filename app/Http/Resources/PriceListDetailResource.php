<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceListDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'day' => $this->day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'price' => $this->price,
            'fixed_price' => $this->fixed_price,
            'current_price' => $this->current_price,
            'price_list_id' => $this->price_list_id,
        ];
    }
}
