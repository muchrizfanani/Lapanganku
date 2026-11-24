<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Booking::query()->with('venue');
        if ($request->user()->role === 'pemilik') {
            $query->whereHas('venue', fn ($venueQuery) => $venueQuery->where('owner_id', $request->user()->id));
        } else {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json(['data' => $query->latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'player_name' => ['required', 'string', 'max:120'],
            'player_phone' => ['required', 'string', 'max:32'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'slots' => ['required', 'array', 'min:1', 'max:12'],
            'slots.*' => ['required', 'string', 'max:32'],
        ]);

        $venue = Venue::findOrFail($validated['venue_id']);
        $totalSlots = count($validated['slots']);
        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'venue_id' => $venue->id,
            'venue_name' => $venue->name,
            'venue_location' => $venue->location,
            'sport' => $venue->sport,
            'player_name' => $validated['player_name'],
            'player_phone' => $validated['player_phone'],
            'booking_date' => $validated['booking_date'],
            'slots' => $validated['slots'],
            'total_slots' => $totalSlots,
            'price_per_hour' => $venue->price,
            'total_price' => $venue->price * $totalSlots,
            'status' => 'pending',
            'payment_method' => 'Bayar di Tempat (COD)',
        ]);

        return response()->json(['data' => $booking], 201);
    }

    public function show(Request $request, Booking $booking): JsonResponse
    {
        $this->ensureCanView($booking, $request);

        return response()->json(['data' => $booking->load('venue')]);
    }

    public function update(Request $request, Booking $booking): JsonResponse
    {
        $this->ensureOwnerCanManage($booking, $request);
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'rejected', 'completed'])],
        ]);
        $booking->update($validated);

        return response()->json(['data' => $booking]);
    }

    public function destroy(Request $request, Booking $booking): JsonResponse
    {
        if ($request->user()->role === 'pemilik') {
            $this->ensureOwnerCanManage($booking, $request);
        } elseif ($booking->user_id !== $request->user()->id) {
            abort(403);
        }

        $booking->delete();

        return response()->json(['message' => 'Booking berhasil dihapus.']);
    }

    private function ensureCanView(Booking $booking, Request $request): void
    {
        if ($request->user()->role === 'pemilik') {
            $this->ensureOwnerCanManage($booking, $request);

            return;
        }

        abort_if($booking->user_id !== $request->user()->id, 403);
    }

    private function ensureOwnerCanManage(Booking $booking, Request $request): void
    {
        abort_if($request->user()->role !== 'pemilik', 403);
        abort_if(
            $booking->venue === null
                || $booking->venue->owner_id !== $request->user()->id,
            403,
        );
    }
}
