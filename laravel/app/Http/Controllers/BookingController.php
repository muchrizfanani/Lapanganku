<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    private const SLOTS = [
        '08:00 - 09:00',
        '09:00 - 10:00',
        '10:00 - 11:00',
        '14:00 - 15:00',
        '15:00 - 16:00',
        '16:00 - 17:00',
        '19:00 - 20:00',
        '20:00 - 21:00',
    ];

    public function create(Request $request, Venue $venue): View
    {
        $date = $request->query('date', now()->toDateString());
        $validated = validator(['date' => $date], [
            'date' => ['required', 'date', 'after_or_equal:today'],
        ])->validate();

        return view('bookings.create', [
            'venue' => $venue,
            'bookingDate' => $validated['date'],
            'slots' => self::SLOTS,
            'occupiedSlots' => $this->occupiedSlots($venue, $validated['date']),
        ]);
    }

    public function store(Request $request, Venue $venue): RedirectResponse
    {
        $validated = $request->validate([
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'slots' => ['required', 'array', 'min:1', 'max:8'],
            'slots.*' => ['required', 'string', Rule::in(self::SLOTS), 'distinct'],
            'player_name' => ['required', 'string', 'max:120'],
            'player_phone' => ['required', 'string', 'max:32'],
        ]);

        $booking = DB::transaction(function () use ($request, $venue, $validated): Booking {
            $lockedVenue = Venue::query()->lockForUpdate()->findOrFail($venue->id);
            $occupiedSlots = $this->occupiedSlots($lockedVenue, $validated['booking_date']);
            $conflicts = array_values(array_intersect($validated['slots'], $occupiedSlots));

            if ($conflicts !== []) {
                throw ValidationException::withMessages([
                    'slots' => 'Slot berikut sudah terisi: '.implode(', ', $conflicts).'. Silakan pilih slot lain.',
                ]);
            }

            $slotCount = count($validated['slots']);

            return Booking::create([
                'user_id' => $request->user()->id,
                'venue_id' => $lockedVenue->id,
                'venue_name' => $lockedVenue->name,
                'venue_location' => $lockedVenue->location,
                'sport' => $lockedVenue->sport,
                'player_name' => $validated['player_name'],
                'player_phone' => $validated['player_phone'],
                'booking_date' => $validated['booking_date'],
                'slots' => $validated['slots'],
                'total_slots' => $slotCount,
                'price_per_hour' => $lockedVenue->price,
                'total_price' => $lockedVenue->price * $slotCount,
                'status' => 'pending',
                'payment_method' => 'Bayar di Tempat (COD)',
            ]);
        });

        return redirect()->route('player.dashboard')
            ->with('status', 'Booking berhasil dibuat dan menunggu konfirmasi pemilik.');
    }

    /**
     * @return list<string>
     */
    private function occupiedSlots(Venue $venue, string $date): array
    {
        return Booking::query()
            ->where('venue_id', $venue->id)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['slots'])
            ->flatMap(fn (Booking $booking): array => $booking->slots ?? [])
            ->unique()
            ->values()
            ->all();
    }
}
