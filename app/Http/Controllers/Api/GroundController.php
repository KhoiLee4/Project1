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

        $grounds = $query->get();
        return GroundResource::collection($grounds);
    }

    public function show(string $id)
    {
        $ground = Ground::with(['venue', 'category'])->findOrFail($id);
        return new GroundResource($ground);
    }
}
