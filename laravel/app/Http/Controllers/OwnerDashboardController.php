<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OwnerDashboardController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::query()
            ->with('venue')
            ->whereHas('venue', fn ($query) => $query
                ->whereNull('owner_id')
                ->orWhere('owner_id', auth()->id()))
            ->latest()
            ->get();

        return view('owner.dashboard', [
            'bookings' => $bookings,
            'pendingCount' => $bookings->where('status', 'pending')->count(),
            'confirmedCount' => $bookings->where('status', 'confirmed')->count(),
            'estimatedRevenue' => $bookings
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price'),
        ]);
    }

    public function updateBookingStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['confirmed', 'rejected'])],
        ]);

        DB::transaction(function () use ($booking, $validated): void {
            $lockedBooking = Booking::query()->lockForUpdate()->with('venue')->findOrFail($booking->id);
            abort_if(
                $lockedBooking->venue === null
                    || ($lockedBooking->venue->owner_id !== null
                        && $lockedBooking->venue->owner_id !== auth()->id()),
                403,
            );
            abort_unless($lockedBooking->status === 'pending', 409);

            $lockedBooking->update($validated);
        });

        return redirect()->route('owner.dashboard')
            ->with('status', 'Status booking berhasil diperbarui.');
    }
}
