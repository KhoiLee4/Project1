<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function predict(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:1|max:255',
        ]);
        
        $query = $validated['query'];
        
        $venues = Venue::where('name', 'like', '%' . $query . '%')
            ->orWhere('address', 'like', '%' . $query . '%')
            ->orWhere('city', 'like', '%' . $query . '%')
            ->orWhere('district', 'like', '%' . $query . '%')
            ->orWhereHas('categories', function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->pluck('id')
            ->toArray();
        
        return response()->json([
            'query' => $query,
            'venue_ids' => $venues,
            'count' => count($venues),
        ]);
    }
}
