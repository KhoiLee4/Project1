<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VenueResource;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = Favorite::with(['venue.owner', 'venue.categories', 'venue.images'])
            ->where('user_id', $request->user()->id)
            ->get();

        $venues = $favorites->map(function($favorite) {
            return $favorite->venue;
        })->filter();

        return VenueResource::collection($venues);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'venue_id' => 'required|exists:venues,id',
        ]);

        $existing = Favorite::where('user_id', $request->user()->id)
            ->where('venue_id', $validated['venue_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Venue is already in favorites'
            ], 422);
        }

        $favorite = Favorite::create([
            'user_id' => $request->user()->id,
            'venue_id' => $validated['venue_id'],
        ]);

        return response()->json([
            'message' => 'Venue added to favorites',
            'favorite' => $favorite
        ], 201);
    }

    public function destroy(Request $request, string $venueId)
    {
        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('venue_id', $venueId)
            ->firstOrFail();

        $favorite->delete();

        return response()->json([
            'message' => 'Venue removed from favorites'
        ]);
    }
}
