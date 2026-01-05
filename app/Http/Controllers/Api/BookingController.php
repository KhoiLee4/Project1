<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'event']);

        // Chỉ load ground relationship nếu có ground_id (không phải event booking)
        $query->with(['ground' => function($q) {
            $q->with(['venue', 'category']);
        }]);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('venue_id')) {
            $query->where(function($q) use ($request) {
                // Booking thường: filter qua ground.venue_id
                $q->whereHas('ground', function($subQ) use ($request) {
                    $subQ->where('venue_id', $request->venue_id);
                })
                // Event booking: filter qua event.venue_id
                ->orWhereHas('event', function($subQ) use ($request) {
                    $subQ->where('venue_id', $request->venue_id);
                });
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
        $isEvent = $request->input('is_event', false);
        
        $rules = [
            'is_event' => 'boolean',
            'event_id' => 'nullable|exists:events,id|required_if:is_event,1',
            'target' => 'nullable|string|max:255',
            'customer_note' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'total_price' => 'nullable|numeric|min:0',
        ];

        // Validation rules khác nhau cho booking thường và event booking
        if ($isEvent) {
            // Event booking: date, start_time, end_time, ground_id có thể null
            $rules['date'] = 'nullable|date';
            $rules['start_time'] = 'nullable';
            $rules['end_time'] = 'nullable';
            $rules['ground_id'] = 'nullable|exists:grounds,id';
        } else {
            // Booking thường: date, start_time, end_time, ground_id bắt buộc
            $rules['date'] = 'required|date';
            $rules['start_time'] = 'required';
            $rules['end_time'] = 'required|after:start_time';
            $rules['ground_id'] = 'required|exists:grounds,id';
        }

        $validated = $request->validate($rules);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'Pending';
        $validated['quantity'] = $validated['quantity'] ?? ($isEvent ? 1 : 30);
        $validated['total_price'] = $validated['total_price'] ?? 0;
        
        // Set null cho các field nullable của event booking
        if ($isEvent) {
            $validated['date'] = null;
            $validated['start_time'] = null;
            $validated['end_time'] = null;
            $validated['ground_id'] = null;
            $validated['amount_time'] = null;
        }

        // Tính amount_time chỉ cho booking thường
        if (!$isEvent && isset($validated['start_time']) && isset($validated['end_time']) && $validated['start_time'] && $validated['end_time']) {
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $endTime = \Carbon\Carbon::parse($validated['end_time']);
            $validated['amount_time'] = $startTime->diffInHours($endTime);
        } elseif (!$isEvent) {
            // Booking thường nhưng thiếu time - set default
            $validated['amount_time'] = 0;
        }

        // Check conflict chỉ cho booking thường (có ground_id và time)
        if (!$isEvent && isset($validated['ground_id']) && isset($validated['date']) && isset($validated['start_time']) && isset($validated['end_time'])) {
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $endTime = \Carbon\Carbon::parse($validated['end_time']);
            
            $conflictingBooking = Booking::where('ground_id', $validated['ground_id'])
                ->where('date', $validated['date'])
                ->where('status', '!=', 'Cancelled')
                ->where('is_event', false) // Chỉ check conflict với booking thường
                ->where(function($query) use ($startTime, $endTime) {
                    $query->where(function($q) use ($startTime, $endTime) {
                        $q->whereBetween('start_time', [$startTime->toTimeString(), $endTime->toTimeString()])
                          ->orWhereBetween('end_time', [$startTime->toTimeString(), $endTime->toTimeString()])
                          ->orWhere(function($q2) use ($startTime, $endTime) {
                              $q2->where('start_time', '<=', $startTime->toTimeString())
                                 ->where('end_time', '>=', $endTime->toTimeString());
                          });
                    });
                })
                ->first();

            if ($conflictingBooking) {
                return response()->json([
                    'message' => 'Khung giờ này đã được đặt. Vui lòng chọn khung giờ khác.',
                    'conflicting_booking' => [
                        'id' => $conflictingBooking->id,
                        'start_time' => $conflictingBooking->start_time,
                        'end_time' => $conflictingBooking->end_time,
                    ]
                ], 422);
            }
        }

        // Loại bỏ các field null trước khi tạo booking (tránh lỗi khi load relationships)
        $bookingData = $validated;
        if ($isEvent) {
            // Event booking: không cần ground relationship
            $booking = Booking::create($bookingData);
            $booking->load(['user', 'event']);
        } else {
            // Booking thường: load đầy đủ relationships
            $booking = Booking::create($bookingData);
            $booking->load(['user', 'ground.venue', 'ground.category', 'event']);
        }

        return new BookingResource($booking);
    }

    public function show(string $id)
    {
        $booking = Booking::with(['user', 'event', 'payments'])
            ->with(['ground' => function($q) {
                $q->with(['venue', 'category']);
            }])
            ->findOrFail($id);

        return new BookingResource($booking);
    }

    public function update(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $isEvent = $booking->is_event;
        
        $rules = [
            'target' => 'nullable|string|max:255',
            'customer_note' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:Pending,Confirmed,Cancelled,Completed',
            'total_price' => 'nullable|numeric|min:0',
        ];

        // Validation rules khác nhau cho booking thường và event booking
        if ($isEvent) {
            $rules['date'] = 'nullable|date';
            $rules['start_time'] = 'nullable';
            $rules['end_time'] = 'nullable';
            $rules['ground_id'] = 'nullable|exists:grounds,id';
        } else {
            $rules['date'] = 'sometimes|date';
            $rules['start_time'] = 'sometimes';
            $rules['end_time'] = 'sometimes|after:start_time';
            $rules['ground_id'] = 'sometimes|exists:grounds,id';
        }

        $validated = $request->validate($rules);

        // Tính amount_time chỉ cho booking thường
        if (!$isEvent && isset($validated['start_time']) && isset($validated['end_time']) && $validated['start_time'] && $validated['end_time']) {
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $endTime = \Carbon\Carbon::parse($validated['end_time']);
            $validated['amount_time'] = $startTime->diffInHours($endTime);
        } elseif ($isEvent) {
            // Event booking không có amount_time
            $validated['amount_time'] = null;
        }

        $booking->update($validated);
        
        // Load relationships tùy theo loại booking
        if ($isEvent) {
            $booking->load(['user', 'event', 'payments']);
        } else {
            $booking->load(['user', 'ground.venue', 'ground.category', 'event', 'payments']);
        }

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
        try {
            $bookings = Booking::with(['event', 'payments'])
                ->with(['ground' => function($q) {
                    $q->with(['venue', 'category']);
                }])
                ->where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            return BookingResource::collection($bookings);
        } catch (\Exception $e) {
            Log::error('Error in myBookings: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'message' => 'Error loading bookings',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function confirm(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->update(['status' => 'Confirmed']);

        // Load relationships tùy theo loại booking
        if ($booking->is_event) {
            $booking->load(['user', 'event', 'payments']);
        } else {
            $booking->load(['user', 'ground.venue', 'ground.category', 'event', 'payments']);
        }

        return new BookingResource($booking);
    }

    public function cancel(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->update(['status' => 'Cancelled']);

        // Load relationships tùy theo loại booking
        if ($booking->is_event) {
            $booking->load(['user', 'event', 'payments']);
        } else {
            $booking->load(['user', 'ground.venue', 'ground.category', 'event', 'payments']);
        }

        return new BookingResource($booking);
    }
}
