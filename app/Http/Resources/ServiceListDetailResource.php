<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceListDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'wholesale' => $this->wholesale,
            'unit_wholesale' => $this->unit_wholesale,
            'retail' => $this->retail,
            'unit_retail' => $this->unit_retail,
            'service_list_id' => $this->service_list_id,
        ];
    }
}
