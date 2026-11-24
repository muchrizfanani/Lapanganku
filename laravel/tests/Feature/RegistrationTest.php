<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_can_register_with_contact_details(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Pemain Baru',
            'email' => 'pemain@example.test',
            'phone' => '08123456789',
            'role' => 'pemain',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('player.dashboard'));

        $this->assertAuthenticatedAs(User::query()->where('email', 'pemain@example.test')->firstOrFail());
        $this->assertDatabaseHas('users', [
            'email' => 'pemain@example.test',
            'phone' => '08123456789',
            'role' => 'pemain',
        ]);
    }

    public function test_owner_registration_requires_a_venue_name_and_opens_owner_dashboard(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Pemilik Baru',
                'email' => 'pemilik@example.test',
                'role' => 'pemilik',
                'venue_name' => 'Arena Baru',
                'password' => 'secure-password',
                'password_confirmation' => 'secure-password',
            ])
            ->assertRedirect(route('owner.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'pemilik@example.test',
            'venue_name' => 'Arena Baru',
            'role' => 'pemilik',
        ]);
    }
}
