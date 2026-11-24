<?php

namespace Tests\Feature;

use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_shows_login_menu_and_database_venues(): void
    {
        Venue::create([
            'name' => 'Lapangan Landing Test',
            'sport' => 'Futsal',
            'price' => 100000,
            'location' => 'Bandung',
            'address' => 'Jalan Olahraga 1',
            'status' => 'available',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Masuk')
            ->assertSee('Cari &amp; Booking Lapangan Olahraga', false)
            ->assertSee('Tersedia')
            ->assertSee('Lapangan Landing Test');
    }

    public function test_landing_page_filters_venues_by_sport_and_location(): void
    {
        Venue::create([
            'name' => 'Futsal Bandung',
            'sport' => 'Futsal',
            'price' => 100000,
            'location' => 'Bandung',
            'address' => 'Jalan Olahraga 1',
            'status' => 'available',
        ]);
        Venue::create([
            'name' => 'Badminton Jakarta',
            'sport' => 'Badminton',
            'price' => 80000,
            'location' => 'Jakarta',
            'address' => 'Jalan Olahraga 2',
            'status' => 'available',
        ]);

        $this->get('/?sport=futsal&location=Bandung')
            ->assertOk()
            ->assertSee('Futsal Bandung')
            ->assertDontSee('Badminton Jakarta');
    }
}
