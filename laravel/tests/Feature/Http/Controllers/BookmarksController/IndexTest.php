<?php

namespace Tests\Feature\Http\Controllers\BookmarksController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexRedirectsToLoginForGuests(): void
    {
        $response = $this->get(route('index'));
        $response->assertRedirect(route('auth.login'));
        $response->assertStatus(302);
    }

    public function testIndexAvailableForLoggedInUsers(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('index'));
        $response->assertStatus(200);
    }
}
