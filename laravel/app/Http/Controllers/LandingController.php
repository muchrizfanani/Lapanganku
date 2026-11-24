<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'sport' => ['nullable', 'in:all,futsal,badminton,basket'],
            'location' => ['nullable', 'string', 'max:120'],
        ]);
        $sport = $filters['sport'] ?? 'all';
        $location = trim($filters['location'] ?? '');

        $venueQuery = Venue::query();
        if ($sport !== 'all') {
            $venueQuery->whereRaw('LOWER(sport) LIKE ?', ['%'.$sport.'%']);
        }
        if ($location !== '') {
            $venueQuery->where(function ($query) use ($location): void {
                $query->where('name', 'like', '%'.$location.'%')
                    ->orWhere('location', 'like', '%'.$location.'%');
            });
        }

        return view('landing', [
            'venues' => $venueQuery->orderBy('name')->get(),
            'categories' => [
                ['name' => 'Futsal', 'sport' => 'futsal', 'icon' => '⚽', 'description' => 'Temukan lapangan futsal untuk bermain bersama tim.'],
                ['name' => 'Badminton', 'sport' => 'badminton', 'icon' => '🏸', 'description' => 'Pilih lapangan badminton yang nyaman dan dekat.'],
                ['name' => 'Basket', 'sport' => 'basket', 'icon' => '🏀', 'description' => 'Cari lapangan basket indoor maupun outdoor.'],
            ],
            'selectedSport' => $sport,
            'location' => $location,
            'venueCount' => Venue::query()->count(),
            'averageRating' => Venue::query()->avg('rating'),
        ]);
    }
}
