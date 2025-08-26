<?php

namespace Tests\Feature\Http\Controllers\BookmarksController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateRedirectsToLoginForGuests(): void
    {
        $response = $this->get(route('bookmarks.create'));
        $response->assertRedirect(route('auth.login'));
        $response->assertStatus(302);
    }

    public function testCreateAvailableForLoggedInUsers(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('bookmarks.create'));
        $response->assertStatus(200);
    }
}
