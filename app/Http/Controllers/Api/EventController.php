<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('venue_id')) {
            $query->whereHas('bookings.ground', function($q) use ($request) {
                $q->where('venue_id', $request->venue_id);
            });
        }

        $events = $query->paginate($request->get('per_page', 15));
        return EventResource::collection($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'ticket_number' => 'required|integer|min:1',
            'level' => 'nullable|string|max:255',
        ]);

        $event = Event::create($validated);
        return new EventResource($event);
    }

    public function show(string $id)
    {
        $event = Event::findOrFail($id);
        return new EventResource($event);
    }

    public function update(Request $request, string $id)
    {
        $event = Event::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'ticket_number' => 'required|integer|min:1',
            'level' => 'nullable|string|max:255',
        ]);

        $event->update($validated);
        return new EventResource($event);
    }

    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }
}
