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
        $categoryId = $request->get('category_id');
        
        $bookingsQuery = \App\Models\Booking::whereHas('ground', function ($query) use ($venueId) {
            $query->where('venue_id', $venueId);
        })
        ->where('date', $date)
        ->where('status', '!=', 'Cancelled')
        ->where('is_event', false)
        ->with(['user', 'ground']);
        
        if ($categoryId) {
            $bookingsQuery->whereHas('ground', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }
        
        $bookings = $bookingsQuery->get();
        
        $bookedSlots = [];
        $lockedSlots = [];
        $eventSlots = [];
        
        $timeSlots = [
            '6:00', '6:30', '7:00', '7:30', '8:00', '8:30', '9:00', '9:30',
            '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
            '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30',
            '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30',
            '22:00', '22:30'
        ];
        
        foreach ($bookings as $booking) {
            if (!$booking->ground_id || !$booking->start_time || !$booking->end_time) {
                continue;
            }
            
            if ($booking->status !== 'Pending' && $booking->status !== 'Confirmed') {
                continue;
            }
            
            $groundId = $booking->ground_id;
            $startTime = \Carbon\Carbon::parse($booking->start_time);
            $endTime = \Carbon\Carbon::parse($booking->end_time);
            
            foreach ($timeSlots as $slot) {
                $slotParts = explode(':', $slot);
                $slotCarbon = \Carbon\Carbon::createFromTime((int)$slotParts[0], (int)$slotParts[1]);
                $nextSlotCarbon = $slotCarbon->copy()->addMinutes(30);
                
                if ($slotCarbon->lt($endTime) && $nextSlotCarbon->gt($startTime)) {
                    $slotKey = "{$groundId}-{$slot}";
                    $bookedSlots[] = $slotKey;
                }
            }
        }
        
        return response()->json([
            'booked' => array_unique($bookedSlots),
            'locked' => $lockedSlots,
            'events' => $eventSlots,
        ]);
    }

    public function calculatePrice(string $venueId, Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|string',
            'end_time' => 'required|string|after:start_time',
            'ground_id' => 'required|exists:grounds,id',
            'category_id' => 'sometimes|exists:categories,id',
            'target' => 'nullable|string',
        ]);
        
        $ground = \App\Models\Ground::with(['venue', 'category'])->findOrFail($validated['ground_id']);
        
        if ($ground->venue_id != $venueId) {
            return response()->json([
                'message' => 'Ground does not belong to this venue'
            ], 422);
        }
        
        $venueId = $ground->venue_id;
        $categoryId = $validated['category_id'] ?? $ground->category_id;
        
        $bookingDate = \Carbon\Carbon::parse($validated['date']);
        $dayOfWeek = $bookingDate->dayOfWeek;
        
        $dayGroup = 'Mon-Thu';
        if ($dayOfWeek >= 5) {
            $dayGroup = 'Fri-Sun';
        }
        
        $startTime = \Carbon\Carbon::parse($validated['start_time']);
        $endTime = \Carbon\Carbon::parse($validated['end_time']);
        $totalHours = $endTime->diffInMinutes($startTime) / 60;
        
        $prices = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $categoryId) {
            $query->where('venues.id', $venueId)
                  ->where('venues_categories.category_id', $categoryId);
        })
        ->where(function($query) use ($bookingDate, $dayGroup) {
            $query->where('date', $bookingDate->toDateString())
                  ->orWhere(function($q) use ($dayGroup) {
                      $q->whereNull('date')->where('day', $dayGroup);
                  });
        })
        ->orderBy('start_time')
        ->get();
        
        if ($prices->isEmpty()) {
            $prices = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $categoryId) {
                $query->where('venues.id', $venueId)
                      ->where('venues_categories.category_id', $categoryId);
            })
            ->where('day', $dayGroup)
            ->orderBy('start_time')
            ->get();
        }
        
        if ($prices->isEmpty()) {
            $prices = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $categoryId) {
                $query->where('venues.id', $venueId)
                      ->where('venues_categories.category_id', $categoryId);
            })
            ->orderBy('start_time')
            ->get();
        }
        
        $totalPrice = 0;
        $priceDetails = [];
        
        if ($prices->isNotEmpty()) {
            $currentTime = $startTime->copy();
            
            while ($currentTime < $endTime) {
                $matchingPrice = $prices->first(function($price) use ($currentTime) {
                    $priceStart = \Carbon\Carbon::parse($price->start_time);
                    $priceEnd = \Carbon\Carbon::parse($price->end_time);
                    return $currentTime->format('H:i') >= $priceStart->format('H:i') && 
                           $currentTime->format('H:i') < $priceEnd->format('H:i');
                });
                
                if (!$matchingPrice) {
                    $matchingPrice = $prices->first();
                }
                
                if ($matchingPrice) {
                    $nextBoundary = $endTime->copy();
                    
                    foreach ($prices as $price) {
                        $priceStart = \Carbon\Carbon::parse($price->start_time);
                        $priceEnd = \Carbon\Carbon::parse($price->end_time);
                        
                        if ($priceStart->gt($currentTime) && $priceStart->lt($nextBoundary)) {
                            $nextBoundary = $priceStart->copy();
                        }
                        if ($priceEnd->gt($currentTime) && $priceEnd->lt($nextBoundary) && $priceEnd->gt($priceStart)) {
                            $nextBoundary = $priceEnd->copy();
                        }
                    }
                    
                    $hoursInSegment = $currentTime->diffInMinutes($nextBoundary) / 60;
                    $unitPrice = $matchingPrice->current_price ?? $matchingPrice->fixed_price ?? 0;
                    $segmentPrice = $unitPrice * $hoursInSegment;
                    $totalPrice += $segmentPrice;
                    
                    $priceDetails[] = [
                        'start_time' => $currentTime->format('H:i'),
                        'end_time' => $nextBoundary->format('H:i'),
                        'hours' => round($hoursInSegment, 2),
                        'unit_price' => $unitPrice,
                        'price' => round($segmentPrice, 2),
                    ];
                    
                    $currentTime = $nextBoundary;
                } else {
                    break;
                }
            }
        }
        
        return response()->json([
            'totalPrice' => round($totalPrice, 2),
            'totalHours' => round($totalHours, 2),
            'unit_price' => $totalHours > 0 ? round($totalPrice / $totalHours, 2) : 0,
            'price_details' => $priceDetails,
        ]);
    }

    public function priceInfo(string $venueId, string $categoryId)
    {
        $venue = Venue::findOrFail($venueId);
        $category = \App\Models\Category::findOrFail($categoryId);
        
        $pivot = \Illuminate\Support\Facades\DB::table('venues_categories')
            ->where('venue_id', $venueId)
            ->where('category_id', $categoryId)
            ->first();
        
        $priceId = $pivot->price_id ?? null;
        
        $prices = [];
        if ($priceId) {
            $prices = \App\Models\Price::where('id', $priceId)->get();
        } else {
            $prices = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $categoryId) {
                $query->where('venues.id', $venueId)
                      ->where('venues_categories.category_id', $categoryId);
            })->get();
        }
        
        return response()->json([
            'venueId' => $venueId,
            'categoryId' => $categoryId,
            'priceId' => $priceId,
            'prices' => $prices->map(function($price) {
                return [
                    'id' => $price->id,
                    'date' => $price->date,
                    'day' => $price->day,
                    'start_time' => $price->start_time,
                    'end_time' => $price->end_time,
                    'fixed_price' => $price->fixed_price,
                    'current_price' => $price->current_price,
                ];
            }),
        ]);
    }
}
