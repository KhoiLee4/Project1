<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RatingResource;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $query = Rating::with(['user.avatar', 'venue']);

        if ($request->has('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $ratings = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return RatingResource::collection($ratings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'venue_id' => 'required|exists:venues,id',
            'star_number' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;

        $existingRating = Rating::where('user_id', $validated['user_id'])
            ->where('venue_id', $validated['venue_id'])
            ->first();

        if ($existingRating) {
            return response()->json([
                'message' => 'You have already rated this venue. Please update your existing rating instead.',
                'rating_id' => $existingRating->id
            ], 422);
        }

        $hasCompletedBooking = \App\Models\Booking::where('user_id', $validated['user_id'])
            ->whereHas('ground', function($query) use ($validated) {
                $query->where('venue_id', $validated['venue_id']);
            })
            ->where('status', 'Completed')
            ->exists();

        if (!$hasCompletedBooking) {
            return response()->json([
                'message' => 'You can only rate venues where you have completed bookings.'
            ], 403);
        }

        $rating = Rating::create($validated);
        $rating->load(['user.avatar', 'venue']);

        return new RatingResource($rating);
    }

    public function show(string $id)
    {
        $rating = Rating::with(['user.avatar', 'venue'])->findOrFail($id);
        return new RatingResource($rating);
    }

    public function update(Request $request, string $id)
    {
        $rating = Rating::findOrFail($id);

        if ($rating->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'star_number' => 'sometimes|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $rating->update($validated);
        $rating->load(['user.avatar', 'venue']);

        return new RatingResource($rating);
    }

    public function destroy(Request $request, string $id)
    {
        $rating = Rating::findOrFail($id);

        if ($rating->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $rating->delete();

        return response()->json(['message' => 'Rating deleted successfully'], 200);
    }
}
