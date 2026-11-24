<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveVenueRequest;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(): View
    {
        return view('venues.index', ['venues' => Venue::query()->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('venues.create');
    }

    public function store(SaveVenueRequest $request): RedirectResponse
    {
        $venue = new Venue($request->validated());
        $venue->owner()->associate($request->user());
        $venue->save();

        return redirect()->route('venues.index')->with('status', 'Venue berhasil ditambahkan.');
    }

    public function show(Venue $venue): View
    {
        return view('venues.show', compact('venue'));
    }

    public function edit(Venue $venue): View
    {
        $this->ensureManageable($venue);

        return view('venues.edit', compact('venue'));
    }

    public function update(SaveVenueRequest $request, Venue $venue): RedirectResponse
    {
        $this->ensureManageable($venue);
        $venue->fill($request->validated());
        $venue->owner_id ??= $request->user()->id;
        $venue->save();

        return redirect()->route('venues.index')->with('status', 'Venue berhasil diperbarui.');
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        $this->ensureManageable($venue);
        $venue->delete();

        return redirect()->route('venues.index')->with('status', 'Venue berhasil dihapus.');
    }

    private function ensureManageable(Venue $venue): void
    {
        abort_if($venue->owner_id !== null && $venue->owner_id !== auth()->id(), 403);
    }
}
