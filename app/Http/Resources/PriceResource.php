<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $formatTime = function($time) {
            if (is_string($time)) {
                return strlen($time) === 5 ? $time . ':00' : $time;
            }
            if ($time instanceof \DateTime || $time instanceof \Carbon\Carbon) {
                return $time->format('H:i:s');
            }
            return $time;
        };

        return [
            'id' => $this->id,
            'day' => $this->day,
            'start_time' => $formatTime($this->start_time),
            'end_time' => $formatTime($this->end_time),
            'fixed_price' => $this->fixed_price ? (float) $this->fixed_price : null,
            'current_price' => (float) $this->current_price,
        ];
    }
}

