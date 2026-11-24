<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveVenueRequest;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Venue::query()->orderBy('name')->get()]);
    }

    public function store(SaveVenueRequest $request): JsonResponse
    {
        $venue = new Venue($request->validated());
        $venue->owner()->associate($request->user());
        $venue->save();

        return response()->json(['data' => $venue], 201);
    }

    public function show(Venue $venue): JsonResponse
    {
        return response()->json(['data' => $venue]);
    }

    public function update(SaveVenueRequest $request, Venue $venue): JsonResponse
    {
        $this->ensureManageable($venue, $request);
        $venue->fill($request->validated());
        $venue->owner_id ??= $request->user()->id;
        $venue->save();

        return response()->json(['data' => $venue]);
    }

    public function destroy(Request $request, Venue $venue): JsonResponse
    {
        $this->ensureManageable($venue, $request);
        $venue->delete();

        return response()->json(['message' => 'Venue berhasil dihapus.']);
    }

    private function ensureManageable(Venue $venue, Request $request): void
    {
        abort_if($venue->owner_id !== null && $venue->owner_id !== $request->user()->id, 403);
    }
}
