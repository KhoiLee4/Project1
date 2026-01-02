<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroundResource;
use App\Models\Ground;
use Illuminate\Http\Request;

class GroundController extends Controller
{
    public function index(Request $request)
    {
        $query = Ground::with(['venue', 'category']);

        if ($request->has('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $grounds = $query->paginate($request->get('per_page', 15));
        return GroundResource::collection($grounds);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'venue_id' => 'required|exists:venues,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $ground = Ground::create($validated);
        $ground->load(['venue', 'category']);
        return new GroundResource($ground);
    }

    public function show(string $id)
    {
        $ground = Ground::with(['venue', 'category'])->findOrFail($id);
        return new GroundResource($ground);
    }

    public function update(Request $request, string $id)
    {
        $ground = Ground::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'venue_id' => 'required|exists:venues,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $ground->update($validated);
        $ground->load(['venue', 'category']);
        return new GroundResource($ground);
    }

    public function destroy(string $id)
    {
        $ground = Ground::findOrFail($id);
        $ground->delete();

        return response()->json([
            'message' => 'Ground deleted successfully'
        ]);
    }
}
