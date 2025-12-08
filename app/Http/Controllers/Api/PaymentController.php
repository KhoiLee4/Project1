<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('booking.user');

        if ($request->has('booking_id')) {
            $query->where('booking_id', $request->booking_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if (!$request->user()->is_admin) {
            $query->whereHas('booking', function($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });
        }

        $payments = $query->paginate($request->get('per_page', 15));
        return PaymentResource::collection($payments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'method' => 'required|in:Cash,Card,Online',
            'note' => 'nullable|string',
            'status' => 'sometimes|in:Pending,Paid,Cancelled,Refunded',
        ]);

        $validated['status'] = $validated['status'] ?? 'Pending';

        $payment = Payment::create($validated);
        return new PaymentResource($payment);
    }

    public function show(Request $request, string $id)
    {
        $payment = Payment::with('booking.user')->findOrFail($id);

        if ($payment->booking->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new PaymentResource($payment);
    }

    public function update(Request $request, string $id)
    {
        $payment = Payment::with('booking')->findOrFail($id);

        if ($payment->booking->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'unit_price' => 'sometimes|numeric|min:0',
            'method' => 'sometimes|in:Cash,Card,Online',
            'note' => 'nullable|string',
            'status' => 'sometimes|in:Pending,Paid,Cancelled,Refunded',
        ]);

        $payment->update($validated);
        return new PaymentResource($payment);
    }
}
