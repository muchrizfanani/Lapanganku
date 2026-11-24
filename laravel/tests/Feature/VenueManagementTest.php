<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_update_and_delete_a_venue(): void
    {
        $owner = User::factory()->create(['role' => 'pemilik']);
        $this->actingAs($owner, 'web');

        $attributes = [
            'name' => 'Lapangan Contoh',
            'sport' => 'Futsal',
            'price' => 125000,
            'location' => 'Bandung',
            'address' => 'Jalan Olahraga 1',
            'facilities' => 'Parkir dan ruang ganti',
            'rating' => 4.5,
            'reviews' => 12,
            'status' => 'available',
        ];

        $this->post(route('venues.store'), $attributes, ['Accept' => 'text/html'])
            ->assertRedirect(route('venues.index'));

        $venue = Venue::query()->where('name', 'Lapangan Contoh')->firstOrFail();
        $this->assertSame($owner->id, $venue->owner_id);

        $this->put(route('venues.update', $venue), [...$attributes, 'price' => 150000], ['Accept' => 'text/html'])
            ->assertRedirect(route('venues.index'));
        $this->assertDatabaseHas('venues', ['id' => $venue->id, 'price' => 150000]);

        $this->delete(route('venues.destroy', $venue), [], ['Accept' => 'text/html'])
            ->assertRedirect(route('venues.index'));
        $this->assertDatabaseMissing('venues', ['id' => $venue->id]);
    }

    public function test_venue_validation_and_owner_access_are_enforced(): void
    {
        $owner = User::factory()->create(['role' => 'pemilik']);
        $this->actingAs($owner)
            ->from(route('venues.create'))
            ->post(route('venues.store'), ['name' => '', 'sport' => 'Futsal'], ['Accept' => 'text/html'])
            ->assertRedirect(route('venues.create'))
            ->assertSessionHasErrors(['name', 'price', 'location', 'address']);

        $player = User::factory()->create(['role' => 'pemain']);
        $this->actingAs($player)->get(route('venues.index'))->assertForbidden();
    }

    public function test_guest_is_sent_to_the_login_page(): void
    {
        $this->get(route('venues.index'), ['Accept' => 'text/html'])->assertRedirect(route('login'));
    }

    public function test_owner_can_render_venue_pages_and_sign_in(): void
    {
        $owner = User::factory()->create(['role' => 'pemilik']);
        $this->get(route('login'), ['Accept' => 'text/html'])->assertOk();

        $this->post(route('login.store'), [
            'email' => $owner->email,
            'password' => 'password',
        ], ['Accept' => 'text/html'])->assertRedirect(route('venues.index'));

        $venue = Venue::create([
            'name' => 'Venue Tampilan',
            'sport' => 'Badminton',
            'price' => 80000,
            'location' => 'Jakarta',
            'address' => 'Jalan Olahraga 9',
            'status' => 'available',
            'owner_id' => $owner->id,
        ]);

        $this->get(route('venues.index'), ['Accept' => 'text/html'])->assertOk();
        $this->get(route('venues.create'), ['Accept' => 'text/html'])->assertOk();
        $this->get(route('venues.show', $venue), ['Accept' => 'text/html'])->assertOk();
        $this->get(route('venues.edit', $venue), ['Accept' => 'text/html'])->assertOk();
    }
}
