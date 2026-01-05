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
            'user' => $this->when($this->user, [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
            ]),
            'user_id' => $this->user_id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'amount_time' => $this->amount_time,
            'is_event' => $this->is_event,
            'ground' => $this->when($this->ground, [
                'id' => $this->ground->id ?? null,
                'name' => $this->ground->name ?? null,
            ]),
            'ground_id' => $this->ground_id,
            'target' => $this->target,
            'customer_note' => $this->customer_note,
            'owner_note' => $this->owner_note,
            'quantity' => $this->quantity,
            'total_price' => $this->total_price ? (float) $this->total_price : 0,
            'status' => $this->status,
            'event' => $this->when($this->event, function() {
                return [
                    'id' => $this->event->id ?? null,
                    'name' => $this->event->name ?? null,
                    'price' => $this->event->price ?? null,
                    'ticket_number' => $this->event->ticket_number ?? null,
                    'level' => $this->event->level ?? null,
                    'start_date' => $this->event->start_date ?? null,
                    'end_date' => $this->event->end_date ?? null,
                ];
            }),
            'event_id' => $this->event_id,
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
