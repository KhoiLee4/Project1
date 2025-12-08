<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'ground.venue', 'ground.category', 'event']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('venue_id')) {
            $query->whereHas('ground', function($q) use ($request) {
                $q->where('venue_id', $request->venue_id);
            });
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate($request->get('per_page', 15));
        return BookingResource::collection($bookings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'ground_id' => 'required|exists:grounds,id',
            'is_event' => 'boolean',
            'event_id' => 'nullable|exists:events,id|required_if:is_event,1',
            'target' => 'nullable|string|max:255',
            'customer_note' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $startTime = \Carbon\Carbon::parse($validated['start_time']);
        $endTime = \Carbon\Carbon::parse($validated['end_time']);
        $amountTime = $startTime->diffInHours($endTime);

        $validated['user_id'] = $request->user()->id;
        $validated['amount_time'] = $amountTime;
        $validated['status'] = 'Pending';
        $validated['quantity'] = $validated['quantity'] ?? 30;

        $booking = Booking::create($validated);
        $booking->load(['user', 'ground.venue', 'ground.category', 'event']);

        return new BookingResource($booking);
    }

    public function show(string $id)
    {
        $booking = Booking::with(['user', 'ground.venue', 'ground.category', 'event', 'payments'])
            ->findOrFail($id);

        return new BookingResource($booking);
    }

    public function update(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'date' => 'sometimes|date',
            'start_time' => 'sometimes',
            'end_time' => 'sometimes|after:start_time',
            'target' => 'nullable|string|max:255',
            'customer_note' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:Pending,Confirmed,Cancelled,Completed',
        ]);

        if (isset($validated['start_time']) && isset($validated['end_time'])) {
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $endTime = \Carbon\Carbon::parse($validated['end_time']);
            $validated['amount_time'] = $startTime->diffInHours($endTime);
        }

        $booking->update($validated);
        $booking->load(['user', 'ground.venue', 'ground.category', 'event', 'payments']);

        return new BookingResource($booking);
    }

    public function destroy(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully'], 200);
    }

    public function myBookings(Request $request)
    {
        $bookings = Booking::with(['ground.venue', 'ground.category', 'event', 'payments'])
            ->where('user_id', $request->user()->id)
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate($request->get('per_page', 15));

        return BookingResource::collection($bookings);
    }
}
