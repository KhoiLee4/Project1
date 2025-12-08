<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index(Request $request)
    {
        $query = Venue::with(['owner', 'categories', 'images', 'grounds', 'ratings']);

        if ($request->has('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('district')) {
            $query->where('district', 'like', '%' . $request->district . '%');
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        $venues = $query->paginate($request->get('per_page', 15));

        return VenueResource::collection($venues);
    }

    public function show(string $id)
    {
        $venue = Venue::with([
            'owner',
            'categories',
            'images',
            'grounds.category',
            'priceLists.details',
            'serviceLists.details',
            'ratings.user',
            'terms'
        ])->findOrFail($id);

        return new VenueResource($venue);
    }
}
