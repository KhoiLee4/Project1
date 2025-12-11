<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageResource;
use App\Http\Resources\PriceListResource;
use App\Http\Resources\PriceResource;
use App\Http\Resources\ServiceListResource;
use App\Http\Resources\TermResource;
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
            'prices',
            'serviceLists.details',
            'ratings.user',
            'terms'
        ])->findOrFail($id);

        return new VenueResource($venue);
    }

    public function services(string $venueId)
    {
        $venue = Venue::findOrFail($venueId);
        $serviceLists = $venue->serviceLists()->with('details')->get();

        return ServiceListResource::collection($serviceLists);
    }

    public function terms(string $venueId)
    {
        $venue = Venue::findOrFail($venueId);
        $terms = $venue->terms()->orderBy('term_category')->orderBy('update_time', 'desc')->get();

        return TermResource::collection($terms);
    }

    public function priceLists(string $venueId)
    {
        $venue = Venue::findOrFail($venueId);
        
        $categories = $venue->categories()->get();
        
        $priceLists = $categories->map(function($category) use ($venueId) {
            $prices = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $category) {
                $query->where('venues.id', $venueId)
                      ->where('venues_categories.category_id', $category->id);
            })->get();
            
            $category->setRelation('prices', $prices);
            return $category;
        })->filter(function($category) {
            return $category->prices->isNotEmpty();
        });

        return PriceListResource::collection($priceLists);
    }

    public function images(string $venueId, Request $request)
    {
        $venue = Venue::findOrFail($venueId);
        
        $query = $venue->images();
        
        if ($request->has('type')) {
            $typeValue = $request->type;
            if (in_array(strtolower($typeValue), ['true', '1', 'yes'], true)) {
                $query->wherePivot('is_image', true);
            } elseif (in_array(strtolower($typeValue), ['false', '0', 'no'], true)) {
                $query->wherePivot('is_image', false);
            }
        }

        $images = $query->get();

        return ImageResource::collection($images);
    }
}
