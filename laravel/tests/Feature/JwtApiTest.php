<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JwtApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_issues_a_token_and_protected_api_accepts_it(): void
    {
        $user = User::factory()->create(['role' => 'pemain']);

        $login = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()->assertJsonPath('token_type', 'bearer');

        $token = $login->json('access_token');
        $this->getJson('/api/venues', ['Authorization' => 'Bearer '.$token])
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_missing_invalid_and_bad_credentials_are_rejected(): void
    {
        $user = User::factory()->create(['role' => 'pemain']);

        $this->getJson('/api/venues')->assertUnauthorized();
        $this->getJson('/api/venues', ['Authorization' => 'Bearer invalid-token'])->assertUnauthorized();
        $expiredToken = JWT::encode([
            'iss' => config('jwt.issuer'),
            'iat' => time() - 7200,
            'exp' => time() - 3600,
            'sub' => (string) $user->id,
        ], config('jwt.secret'), 'HS256');
        $this->getJson('/api/venues', ['Authorization' => 'Bearer '.$expiredToken])->assertUnauthorized();
        $this->postJson('/api/login', [
            'email' => 'wrong@example.test',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    public function test_player_can_create_a_booking_with_server_calculated_price(): void
    {
        $user = User::factory()->create(['role' => 'pemain']);
        $venue = Venue::create([
            'name' => 'Futsal Lapanganku',
            'sport' => 'Futsal',
            'price' => 100000,
            'location' => 'Bandung',
            'address' => 'Jalan Lapangan 1',
            'status' => 'available',
        ]);
        $token = $this->login($user);

        $this->postJson('/api/bookings', [
            'venue_id' => $venue->id,
            'player_name' => 'Pemain Test',
            'player_phone' => '081234567890',
            'booking_date' => now()->addDay()->toDateString(),
            'slots' => ['19:00 - 20:00', '20:00 - 21:00'],
        ], ['Authorization' => 'Bearer '.$token])
            ->assertCreated()
            ->assertJsonPath('data.total_price', 200000)
            ->assertJsonPath('data.total_slots', 2);

        $this->assertSame(1, Booking::query()->count());
    }

    public function test_venue_api_supports_validated_crud_for_an_owner(): void
    {
        $owner = User::factory()->create(['role' => 'pemilik']);
        $token = $this->login($owner);
        $headers = ['Authorization' => 'Bearer '.$token];
        $attributes = [
            'name' => 'Badminton Lapanganku',
            'sport' => 'Badminton',
            'price' => 90000,
            'location' => 'Jakarta',
            'address' => 'Jalan Arena 2',
            'status' => 'available',
        ];

        $created = $this->postJson('/api/venues', $attributes, $headers)
            ->assertCreated()
            ->json('data.id');
        $this->putJson('/api/venues/'.$created, [...$attributes, 'price' => 95000], $headers)
            ->assertOk()
            ->assertJsonPath('data.price', 95000);
        $this->deleteJson('/api/venues/'.$created, [], $headers)
            ->assertOk();
        $this->assertDatabaseMissing('venues', ['id' => $created]);
    }

    public function test_owner_can_update_booking_status_but_player_cannot(): void
    {
        $owner = User::factory()->create(['role' => 'pemilik']);
        $player = User::factory()->create(['role' => 'pemain']);
        $venue = Venue::create([
            'name' => 'Basket Lapanganku',
            'sport' => 'Basket',
            'price' => 110000,
            'location' => 'Depok',
            'address' => 'Jalan Lapangan 3',
            'status' => 'available',
            'owner_id' => $owner->id,
        ]);
        $booking = Booking::create([
            'user_id' => $player->id,
            'venue_id' => $venue->id,
            'venue_name' => $venue->name,
            'venue_location' => $venue->location,
            'sport' => $venue->sport,
            'player_name' => $player->name,
            'player_phone' => '081234567890',
            'booking_date' => now()->addDay()->toDateString(),
            'slots' => ['19:00 - 20:00'],
            'total_slots' => 1,
            'price_per_hour' => $venue->price,
            'total_price' => $venue->price,
            'status' => 'pending',
        ]);

        $this->patchJson('/api/bookings/'.$booking->id, ['status' => 'confirmed'], [
            'Authorization' => 'Bearer '.$this->login($player),
        ])->assertForbidden();

        $this->patchJson('/api/bookings/'.$booking->id, ['status' => 'confirmed'], [
            'Authorization' => 'Bearer '.$this->login($owner),
        ])->assertOk()->assertJsonPath('data.status', 'confirmed');

        $otherOwner = User::factory()->create(['role' => 'pemilik']);
        $otherVenue = Venue::create([
            'name' => 'Venue Pemilik Lain',
            'sport' => 'Futsal',
            'price' => 100000,
            'location' => 'Bogor',
            'address' => 'Jalan Olahraga 4',
            'status' => 'available',
            'owner_id' => $otherOwner->id,
        ]);
        $otherBooking = Booking::create([
            'user_id' => $player->id,
            'venue_id' => $otherVenue->id,
            'venue_name' => $otherVenue->name,
            'venue_location' => $otherVenue->location,
            'sport' => $otherVenue->sport,
            'player_name' => $player->name,
            'player_phone' => '081234567890',
            'booking_date' => now()->addDay()->toDateString(),
            'slots' => ['19:00 - 20:00'],
            'total_slots' => 1,
            'price_per_hour' => $otherVenue->price,
            'total_price' => $otherVenue->price,
            'status' => 'pending',
        ]);
        $ownerHeaders = ['Authorization' => 'Bearer '.$this->login($owner)];

        $this->getJson('/api/bookings', $ownerHeaders)
            ->assertOk()
            ->assertJsonMissing(['id' => $otherBooking->id]);
        $this->patchJson('/api/bookings/'.$otherBooking->id, ['status' => 'confirmed'], $ownerHeaders)
            ->assertForbidden();
    }

    private function login(User $user): string
    {
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk();

        return $response->json('access_token');
    }
}
