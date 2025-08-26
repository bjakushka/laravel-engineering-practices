<?php

namespace Tests\Feature\Http\Controllers\BookmarksController;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateRedirectsToLoginForGuests(): void
    {
        $response = $this->post(route('bookmarks.store'), [
            'url' => 'https://example.com',
            'title' => 'Example',
            'action' => 'create',
        ]);
        $response->assertRedirect(route('auth.login'));
        $response->assertStatus(302);
    }

    public function testCreateAvailableForLoggedInUsers(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('bookmarks.store'));
        $response->assertStatus(302);
    }

    public function testCreateInvalidUrl(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('bookmarks.store'), [
            'url' => 'invalid-url',
            'title' => 'Example',
        ]);

        $response->assertSessionHasErrors([
            'url' => 'The url field must be a valid URL.'
        ]);
        $response->assertStatus(302);
    }

    public function testCreateInvalidTitleEmpty(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('bookmarks.store'), [
            'url' => 'https://example.com',
            'title' => '',
        ]);

        $response->assertSessionHasErrors([
            'title' => 'The title field is required.'
        ]);
        $response->assertStatus(302);
    }

    public function testCreateInvalidTitleTooLong(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('bookmarks.store'), [
            'url' => 'https://example.com',
            'title' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 255 characters.'
        ]);
        $response->assertStatus(302);
    }

    public function testCreateInvalidAction(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('bookmarks.store'), [
            'url' => 'https://example.com',
            'title' => 'Example',
            'action' => 'invalid_action',
        ]);

        $response->assertSessionHasErrors([
            'action' => 'The selected action is invalid.'
        ]);
        $response->assertStatus(302);
    }

    public function testCreateInvalidActionEmpty(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('bookmarks.store'), [
            'url' => 'https://example.com',
            'title' => 'Example',
            'action' => '',
        ]);

        $response->assertSessionHasErrors([
            'action' => 'The action field must be a string.'
        ]);
        $response->assertSessionHasErrors([
            'action' => 'The selected action is invalid.'
        ]);
        $response->assertStatus(302);
    }

    public function testCreateValidDataNoAction(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertDatabaseCount('bookmarks', 0);

        $now = now();
        $response = $this->post(route('bookmarks.store'), [
            'url' => 'https://example.com',
            'title' => 'Example',
        ]);

        $this->assertDatabaseCount('bookmarks', 1);

        $bookmark = Bookmark::query()->first();
        $this->assertNotNull($bookmark, 'Bookmark was not created');
        $this->assertEquals(
            $user->id, $bookmark->user_id, 'User ID does not match'
        );
        $this->assertEquals(
            'https://example.com', $bookmark->url, 'URL does not match'
        );
        $this->assertEquals(
            'Example', $bookmark->title, 'Title does not match'
        );
        $this->assertEquals(
            $now->format('Y-m-d'), $bookmark->created_at->format('Y-m-d'),
            'Creation date does not match'
        );
        $this->assertEquals(
            $bookmark->created_at->format('c'), $bookmark->updated_at->format('c'),
            'Updated at should be the same as created at on creation'
        );
        $this->assertFalse(
            $bookmark->is_read, 'is_read should be false on creation'
        );
        $this->assertNull(
            $bookmark->read_at, 'read_at should be null on creation'
        );

        $response->assertStatus(302);
        $response->assertRedirect(route('index'));
        $response->assertSessionHas('success', 'bookmark created successfully');
    }

    public function testCreateValidDataActionCreate(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertDatabaseCount('bookmarks', 0);

        $now = now();
        $response = $this->post(route('bookmarks.store'), [
            'url' => 'https://example.com',
            'title' => 'Example',
            'action' => 'create',
        ]);

        $this->assertDatabaseCount('bookmarks', 1);

        $bookmark = Bookmark::query()->first();
        $this->assertNotNull($bookmark, 'Bookmark was not created');
        $this->assertEquals(
            $user->id, $bookmark->user_id, 'User ID does not match'
        );
        $this->assertEquals(
            'https://example.com', $bookmark->url, 'URL does not match'
        );
        $this->assertEquals(
            'Example', $bookmark->title, 'Title does not match'
        );
        $this->assertEquals(
            $now->format('Y-m-d'), $bookmark->created_at->format('Y-m-d'),
            'Creation date does not match'
        );
        $this->assertEquals(
            $bookmark->created_at->format('c'), $bookmark->updated_at->format('c'),
            'Updated at should be the same as created at on creation'
        );
        $this->assertFalse(
            $bookmark->is_read, 'is_read should be false on creation'
        );
        $this->assertNull(
            $bookmark->read_at, 'read_at should be null on creation'
        );

        $response->assertStatus(302);
        $response->assertRedirect(route('index'));
        $response->assertSessionHas('success', 'bookmark created successfully');
    }

    public function testCreateValidDataActionCreateContinue(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertDatabaseCount('bookmarks', 0);

        $now = now();
        $response = $this->post(route('bookmarks.store'), [
            'url' => 'https://example.com',
            'title' => 'Example',
            'action' => 'create_continue',
        ]);

        $this->assertDatabaseCount('bookmarks', 1);

        $bookmark = Bookmark::query()->first();
        $this->assertNotNull($bookmark, 'Bookmark was not created');
        $this->assertEquals(
            $user->id, $bookmark->user_id, 'User ID does not match'
        );
        $this->assertEquals(
            'https://example.com', $bookmark->url, 'URL does not match'
        );
        $this->assertEquals(
            'Example', $bookmark->title, 'Title does not match'
        );
        $this->assertEquals(
            $now->format('Y-m-d'), $bookmark->created_at->format('Y-m-d'),
            'Creation date does not match'
        );
        $this->assertEquals(
            $bookmark->created_at->format('c'), $bookmark->updated_at->format('c'),
            'Updated at should be the same as created at on creation'
        );
        $this->assertFalse(
            $bookmark->is_read, 'is_read should be false on creation'
        );
        $this->assertNull(
            $bookmark->read_at, 'read_at should be null on creation'
        );

        $response->assertStatus(302);
        $response->assertRedirect(route('bookmarks.create'));
        $response->assertSessionHas('success', 'bookmark created successfully');
    }
}
