<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportLegacyDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_json_import_is_repeatable_and_does_not_change_source_files(): void
    {
        $venuesPath = base_path('../data/venues.json');
        $bookingsPath = base_path('../data/bookings.json');
        $venuesHash = hash_file('sha256', $venuesPath);
        $bookingsHash = hash_file('sha256', $bookingsPath);

        $this->artisan('app:import-legacy-data')->assertSuccessful();
        $counts = [Venue::query()->count(), Booking::query()->count()];
        $this->artisan('app:import-legacy-data')->assertSuccessful();

        $this->assertSame($counts, [Venue::query()->count(), Booking::query()->count()]);
        $this->assertSame($venuesHash, hash_file('sha256', $venuesPath));
        $this->assertSame($bookingsHash, hash_file('sha256', $bookingsPath));
    }
}
