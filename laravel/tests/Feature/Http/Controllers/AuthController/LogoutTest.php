<?php

namespace Tests\Feature\Http\Controllers\AuthController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testLogoutForGuests(): void
    {
        $response = $this->post(route('auth.logout'));

        $response->assertRedirect(route('auth.login'));
        $response->assertStatus(302);
    }

    public function testLogoutForLoggedInUsers(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('auth.logout'));

        $this->assertFalse(
            $this->isAuthenticated(),
            'User is still authenticated after logout',
        );
        $response->assertRedirect(route('index'));
        $response->assertStatus(302);
    }
}
