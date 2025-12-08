<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
            ],
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'amount_time' => $this->amount_time,
            'is_event' => $this->is_event,
            'ground' => [
                'id' => $this->ground->id ?? null,
                'name' => $this->ground->name ?? null,
            ],
            'target' => $this->target,
            'customer_note' => $this->customer_note,
            'owner_note' => $this->owner_note,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'event' => $this->when($this->event, [
                'id' => $this->event->id ?? null,
                'name' => $this->event->name ?? null,
                'price' => $this->event->price ?? null,
            ]),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
