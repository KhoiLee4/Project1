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

        $events = $query->paginate($request->get('per_page', 15));
        return EventResource::collection($events);
    }

    public function show(string $id)
    {
        $event = Event::findOrFail($id);
        return new EventResource($event);
    }
}
