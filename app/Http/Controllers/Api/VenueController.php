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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'sub_address' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:300',
            'operating_time' => 'required|string|max:100',
            'phone_number1' => 'required|string|max:20',
            'phone_number2' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'deposit' => 'required|numeric|min:0|max:100',
            'owner_id' => 'required|exists:users,id',
        ]);

        $venue = Venue::create($validated);
        $venue->load(['owner', 'categories', 'images']);
        return new VenueResource($venue);
    }

    public function update(Request $request, string $id)
    {
        $venue = Venue::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'sub_address' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:300',
            'operating_time' => 'required|string|max:100',
            'phone_number1' => 'required|string|max:20',
            'phone_number2' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'deposit' => 'required|numeric|min:0|max:100',
            'owner_id' => 'required|exists:users,id',
        ]);

        $venue->update($validated);
        $venue->load(['owner', 'categories', 'images']);
        return new VenueResource($venue);
    }

    public function destroy(string $id)
    {
        $venue = Venue::findOrFail($id);
        $venue->delete();

        return response()->json([
            'message' => 'Venue deleted successfully'
        ]);
    }

    public function schedule(string $venueId, Request $request)
    {
        $venue = Venue::findOrFail($venueId);
        $date = $request->get('date', now()->toDateString());
        
        $bookings = \App\Models\Booking::whereHas('ground', function ($query) use ($venueId) {
            $query->where('venue_id', $venueId);
        })
        ->where('date', $date)
        ->where('status', '!=', 'Cancelled')
        ->with(['user', 'ground.category'])
        ->get();
        
        $schedule = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'ground_id' => $booking->ground_id,
                'ground_name' => $booking->ground->name,
                'category_name' => $booking->ground->category->name ?? null,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'user_name' => $booking->user->name ?? null,
                'status' => $booking->status,
            ];
        });
        
        return response()->json([
            'venue_id' => $venueId,
            'date' => $date,
            'schedule' => $schedule,
        ]);
    }

    public function calculatePrice(string $venueId, Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|string',
            'end_time' => 'required|string|after:start_time',
            'ground_id' => 'required|exists:grounds,id',
        ]);
        
        $ground = \App\Models\Ground::with(['venue', 'category'])->findOrFail($validated['ground_id']);
        
        if ($ground->venue_id != $venueId) {
            return response()->json([
                'message' => 'Ground does not belong to this venue'
            ], 422);
        }
        
        $venueId = $ground->venue_id;
        $categoryId = $ground->category_id;
        
        $bookingDate = \Carbon\Carbon::parse($validated['date']);
        $dayOfWeek = $bookingDate->dayOfWeek;
        
        $dayGroup = 'Mon-Thu';
        if ($dayOfWeek >= 5) {
            $dayGroup = 'Fri-Sun';
        }
        
        $startTime = \Carbon\Carbon::parse($validated['start_time']);
        $endTime = \Carbon\Carbon::parse($validated['end_time']);
        $hours = $endTime->diffInHours($startTime);
        $bookingStartTime = $startTime->format('H:i');
        
        $price = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $categoryId) {
            $query->where('venues.id', $venueId)
                  ->where('venues_categories.category_id', $categoryId);
        })
        ->where('day', $dayGroup)
        ->where('start_time', '<=', $bookingStartTime)
        ->where('end_time', '>', $bookingStartTime)
        ->first();
        
        if (!$price) {
            $price = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $categoryId) {
                $query->where('venues.id', $venueId)
                      ->where('venues_categories.category_id', $categoryId);
            })
            ->where('day', $dayGroup)
            ->first();
        }
        
        if (!$price) {
            $price = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $categoryId) {
                $query->where('venues.id', $venueId)
                      ->where('venues_categories.category_id', $categoryId);
            })->first();
        }
        
        if ($price) {
            $unitPrice = $price->current_price ?? $price->fixed_price ?? 0;
            $totalAmount = round($unitPrice * $hours, 2);
            
            return response()->json([
                'unit_price' => $unitPrice,
                'hours' => $hours,
                'total_amount' => $totalAmount,
                'price_details' => [
                    'day_group' => $dayGroup,
                    'fixed_price' => $price->fixed_price,
                    'current_price' => $price->current_price,
                ]
            ]);
        }
        
        return response()->json([
            'unit_price' => 0,
            'hours' => $hours,
            'total_amount' => 0,
            'message' => 'No price found for this venue and category'
        ]);
    }
}
