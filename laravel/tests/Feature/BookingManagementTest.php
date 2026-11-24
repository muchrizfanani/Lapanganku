<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_book_slots_and_see_the_booking_in_their_history(): void
    {
        $player = User::factory()->create(['role' => 'pemain', 'phone' => '08123456789']);
        $venue = $this->createVenue();
        $date = now()->addDay()->toDateString();

        $this->actingAs($player)
            ->get(route('bookings.create', ['venue' => $venue, 'date' => $date]))
            ->assertOk()
            ->assertSee('Pilih slot jam')
            ->assertSee('08:00 - 09:00');

        $this->actingAs($player)
            ->post(route('bookings.store', $venue), [
                'booking_date' => $date,
                'slots' => ['08:00 - 09:00', '09:00 - 10:00'],
                'player_name' => $player->name,
                'player_phone' => $player->phone,
            ])
            ->assertRedirect(route('player.dashboard'));

        $this->assertDatabaseHas('bookings', [
            'user_id' => $player->id,
            'venue_id' => $venue->id,
            'total_slots' => 2,
            'total_price' => 200000,
            'status' => 'pending',
        ]);
        $this->get(route('player.dashboard'))
            ->assertOk()
            ->assertSee($venue->name)
            ->assertSee('Menunggu konfirmasi');
    }

    public function test_guest_is_sent_to_login_and_returns_to_the_booking_form(): void
    {
        $player = User::factory()->create(['role' => 'pemain']);
        $venue = $this->createVenue();

        $this->get(route('bookings.create', $venue))
            ->assertRedirect(route('login'));

        $this->post(route('login.store'), [
            'email' => $player->email,
            'password' => 'password',
        ])->assertRedirect(route('bookings.create', $venue));
    }

    public function test_player_cannot_book_a_slot_that_is_already_pending(): void
    {
        $player = User::factory()->create(['role' => 'pemain']);
        $venue = $this->createVenue();
        $date = now()->addDay()->toDateString();
        Booking::create([
            'user_id' => User::factory()->create(['role' => 'pemain'])->id,
            'venue_id' => $venue->id,
            'venue_name' => $venue->name,
            'venue_location' => $venue->location,
            'sport' => $venue->sport,
            'player_name' => 'Pemain Pertama',
            'player_phone' => '0811111111',
            'booking_date' => $date,
            'slots' => ['08:00 - 09:00'],
            'total_slots' => 1,
            'price_per_hour' => $venue->price,
            'total_price' => $venue->price,
            'status' => 'pending',
            'payment_method' => 'Bayar di Tempat (COD)',
        ]);

        $this->actingAs($player)
            ->from(route('bookings.create', ['venue' => $venue, 'date' => $date]))
            ->post(route('bookings.store', $venue), [
                'booking_date' => $date,
                'slots' => ['08:00 - 09:00'],
                'player_name' => $player->name,
                'player_phone' => '08123456789',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('slots');

        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_owner_can_review_and_confirm_a_booking(): void
    {
        $owner = User::factory()->create(['role' => 'pemilik']);
        $venue = $this->createVenue(['owner_id' => $owner->id]);
        $booking = Booking::create([
            'user_id' => User::factory()->create(['role' => 'pemain'])->id,
            'venue_id' => $venue->id,
            'venue_name' => $venue->name,
            'venue_location' => $venue->location,
            'sport' => $venue->sport,
            'player_name' => 'Pemain LapanganKu',
            'player_phone' => '08123456789',
            'booking_date' => now()->addDay()->toDateString(),
            'slots' => ['08:00 - 09:00'],
            'total_slots' => 1,
            'price_per_hour' => $venue->price,
            'total_price' => $venue->price,
            'status' => 'pending',
            'payment_method' => 'Bayar di Tempat (COD)',
        ]);

        $this->actingAs($owner)
            ->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('Pemain LapanganKu')
            ->assertSee('Perlu konfirmasi');

        $this->post(route('bookings.status', $booking), ['status' => 'confirmed'])
            ->assertRedirect(route('owner.dashboard'));

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'confirmed']);
    }

    private function createVenue(array $attributes = []): Venue
    {
        return Venue::create(array_merge([
            'name' => 'Venue Booking Test',
            'sport' => 'Futsal',
            'price' => 100000,
            'location' => 'Bandung',
            'address' => 'Jalan Olahraga 1',
            'status' => 'available',
        ], $attributes));
    }
}
