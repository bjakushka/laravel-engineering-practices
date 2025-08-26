<?php

namespace Tests\Feature\Http\Controllers\BookmarksController;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function testDestroyRedirectsToLoginForGuests(): void
    {
        $response = $this->delete(route('bookmarks.destroy', ['id' => 1]));
        $response->assertRedirect(route('auth.login'));
        $response->assertStatus(302);
    }

    public function testDestroyAvailableForLoggedInUsers(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->delete(route('bookmarks.destroy', ['id' => 1]));
        $response->assertStatus(302);
    }

    public function testDestroyInvalidNoIdRoute(): void {
        $this->expectException(UrlGenerationException::class);
        route('bookmarks.destroy');
    }

    public function testDestroyInvalidWrongId(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->delete(route('bookmarks.destroy', ['id' => 0]));
        $response->assertStatus(302);
        $response->assertRedirect(route('index'));
        $response->assertSessionHas([
            'error' => 'bookmark not found or could not be deleted'
        ]);
    }

    public function testDestroyInvalidAnotherUserRecord(): void {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $this->actingAs($user1);

        /** @var Bookmark $bookmark1 */
        $user1->bookmarks()->create([
            'url' => 'https://user1.example.com',
            'title' => 'User 1 Bookmark',
        ]);
        /** @var Bookmark $bookmark2 */
        $bookmark2 = $user2->bookmarks()->create([
            'url' => 'https://user2.example.com',
            'title' => 'User 2 Bookmark',
        ]);

        $this->assertDatabaseCount('bookmarks', 2);

        $response = $this->delete(route('bookmarks.destroy', [
            'id' => $bookmark2->id
        ]));
        $response->assertStatus(302);
        $response->assertRedirect(route('index'));
        $response->assertSessionHas([
            'error' => 'bookmark not found or could not be deleted'
        ]);

        $this->assertDatabaseCount('bookmarks', 2);
    }

    public function testDestroySuccess(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Bookmark $bookmark1 */
        $bookmarks = $user->bookmarks()->createMany([
            [
                'url' => 'https://user.example.com/1',
                'title' => 'User Bookmark 1',
            ],
            [
                'url' => 'https://user.example.com/2',
                'title' => 'User Bookmark 2',
            ],
        ]);

        $this->assertDatabaseCount('bookmarks', 2);

        $response = $this->delete(route('bookmarks.destroy', [
            'id' => $bookmarks[0]->id
        ]));
        $response->assertStatus(302);
        $response->assertRedirect(route('index'));
        $response->assertSessionHas([
            'success' => 'bookmark deleted successfully'
        ]);

        $this->assertDatabaseCount('bookmarks', 1);
        $this->assertDatabaseMissing('bookmarks', [
            'id' => $bookmarks[0]->id
        ]);
        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmarks[1]->id
        ]);
    }
}
