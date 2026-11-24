<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function dashboard(): View
    {
        $player = auth()->user();

        return view('player.dashboard', [
            'venues' => Venue::query()->orderBy('name')->get(),
            'bookings' => $player->bookings()->with('venue')->latest()->get(),
        ]);
    }
}
