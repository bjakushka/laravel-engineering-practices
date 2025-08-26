<?php

namespace Tests\Feature\Services;

use App\DTO\PaginatedResult;
use App\Models\Bookmark;
use App\Models\User;
use App\Services\AuthService;
use App\Services\BookmarkService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(AuthService::class)]
class BookmarksServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookmarkService $bookmarkService;

    protected function setUp(): void {
        parent::setUp();

        $this->bookmarkService = new BookmarkService();
    }

    public function testGetUserBookmarks(): void {
        $count = 5;
        $user = User::factory()->create();
        Bookmark::factory()->count($count)->create(['user_id' => $user->id]);

        $bookmarks = $this->bookmarkService->getUserBookmarks($user->id, 1, $count);

        $this->assertInstanceOf(
            PaginatedResult::class, $bookmarks,
            'Should return a PaginatedResult instance'
        );
        $this->assertCount(
            $count, $bookmarks->items,
            "Should return $count bookmarks, as requested"
        );
        foreach ($bookmarks->items as $bookmark) {
            $this->assertInstanceOf(Bookmark::class, $bookmark);
            $this->assertEquals($user->id, $bookmark->user_id);
        }
    }

    public function testGetEmptyBookmarks(): void {
        $user = User::factory()->create();

        $bookmarks = $this->bookmarkService->getUserBookmarks($user->id);

        $this->assertCount(
            0, $bookmarks->items,
            "Should return 0 bookmarks for a user with none"
        );
    }

    public function testGetUserBookmarksNotFound(): void {
        $nonExistentUserId = 9999;

        $bookmarks = $this->bookmarkService->getUserBookmarks($nonExistentUserId);

        $this->assertCount(
            0, $bookmarks->items,
            "Should return 0 bookmarks for a non-existent user"
        );
    }

    public function testGetUserBookmarksIsolation(): void {
        $user1BookmarksCount = 3;
        $user2BookmarksCount = 2;
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Bookmark::factory()->count($user1BookmarksCount)->create([
            'user_id' => $user1->id,
        ]);
        Bookmark::factory()->count($user2BookmarksCount)->create([
            'user_id' => $user2->id,
        ]);

        $user1Bookmarks = $this->bookmarkService->getUserBookmarks($user1->id);
        $user2Bookmarks = $this->bookmarkService->getUserBookmarks($user2->id);

        $this->assertCount(
            $user1BookmarksCount, $user1Bookmarks->items,
            "User 1 should have $user1BookmarksCount bookmarks"
        );
        $this->assertCount(
            $user2BookmarksCount, $user2Bookmarks->items,
            "User 2 should have $user2BookmarksCount bookmarks"
        );

        foreach ($user1Bookmarks->items as $bookmark) {
            $this->assertEquals(
                $user1->id, $bookmark->user_id,
                'Bookmark should belong to User 1'
            );
        }

        foreach ($user2Bookmarks->items as $bookmark) {
            $this->assertEquals(
                $user2->id, $bookmark->user_id,
                'Bookmark should belong to User 2'
            );
        }
    }

    public function testGetUserBookmarksSorting(): void {
        $user = User::factory()->create();
        $bookmark1 = Bookmark::factory()->create([
           'user_id' => $user->id,
           'created_at' => now()->subDays(2),
        ]);
        $bookmark2 = Bookmark::factory()->create([
           'user_id' => $user->id,
           'created_at' => now()->subDays(1),
        ]);
        $bookmark3 = Bookmark::factory()->create([
           'user_id' => $user->id,
           'created_at' => now()->subDays(0),
        ]);


        $bookmarks = $this->bookmarkService->getUserBookmarks($user->id, 1, 3);
        $this->assertEquals(
            $bookmark1->id, $bookmarks->items[2]->id,
            'Oldest bookmark should be last'
        );
        $this->assertEquals(
            $bookmark2->id, $bookmarks->items[1]->id,
            'Middle bookmark should be second'
        );
        $this->assertEquals(
            $bookmark3->id, $bookmarks->items[0]->id,
            'Newest bookmark should be first'
        );
    }

    public function testGetUserBookmarksPaginationStructure(): void {
        $user = User::factory()->create();

        $bookmarks = $this->bookmarkService->getUserBookmarks($user->id);

        $this->assertInstanceOf(
            PaginatedResult::class, $bookmarks,
            'Should return a PaginatedResult instance'
        );
    }

    public function testGetUserBookmarksPaginationDefaults(): void {
        $user = User::factory()->create();
        Bookmark::factory()->count(15)->create(['user_id' => $user->id]);

        $bookmarksPage = $this->bookmarkService->getUserBookmarks($user->id);

        $this->assertCount(
            10, $bookmarksPage->items,
            'Should return 10 bookmarks by default'
        );
        $this->assertEquals(
            1, $bookmarksPage->currentPage,
            'Should return page 1 by default'
        );
        $this->assertEquals(
            10, $bookmarksPage->perPage,
            'Should return 10 bookmarks per page by default'
        );
        $this->assertEquals(
            15, $bookmarksPage->total,
            'Should return total of 15 bookmarks'
        );
    }

    public function testGetUserBookmarksPaginationNavigation(): void {
        $user = User::factory()->create();
        Bookmark::factory()->count(50)->create(['user_id' => $user->id]);

        $bookmarksPage1 = $this->bookmarkService->getUserBookmarks($user->id, 1);
        $bookmarksPage2 = $this->bookmarkService->getUserBookmarks($user->id, 2);

        $this->assertEquals(
            1, $bookmarksPage1->currentPage,
            'Should return page 1 as requested'
        );
        $this->assertEquals(
            10, $bookmarksPage1->perPage,
            'Should return 10 bookmarks per page by default'
        );
        $this->assertEquals(
            50, $bookmarksPage1->total,
            'Should return total of 50 bookmarks'
        );

        $this->assertEquals(
            2, $bookmarksPage2->currentPage,
            'Should return page 2 when requested'
        );
        $this->assertEquals(
            10, $bookmarksPage2->perPage,
            'Should return 10 bookmarks per page by default'
        );
        $this->assertEquals(
            50, $bookmarksPage2->total,
            'Should return total of 50 bookmarks'
        );
    }

    public function testGetUserBookmarksPaginationCustomPerPage(): void {
        $customPerPage = 21;
        $user = User::factory()->create();
        Bookmark::factory()->count(50)->create(['user_id' => $user->id]);

        $bookmarks1 = $this->bookmarkService->getUserBookmarks(
            $user->id, 1, $customPerPage
        );
        $bookmarks2 = $this->bookmarkService->getUserBookmarks(
            $user->id, 2, $customPerPage
        );

        $this->assertCount(
            $customPerPage, $bookmarks1->items,
            "Should return $customPerPage bookmarks as requested for first page"
        );

        $this->assertCount(
            $customPerPage, $bookmarks2->items,
            "Should return $customPerPage bookmarks as requested for second page"
        );
    }

    public function testGetUserBookmarksPaginationLastPagePartial(): void {
        $user = User::factory()->create();
        Bookmark::factory()->count(25)->create(['user_id' => $user->id]);

        $bookmarksPage3 = $this->bookmarkService->getUserBookmarks($user->id, 3);

        $this->assertCount(
            5, $bookmarksPage3->items,
            'Should return 5 bookmarks on the last page when total is not a multiple of perPage'
        );
        $this->assertEquals(
            3, $bookmarksPage3->currentPage,
            'Should return page 3 as requested'
        );
        $this->assertEquals(
            10, $bookmarksPage3->perPage,
            'Should return 10 bookmarks per page by default'
        );
        $this->assertEquals(
            25, $bookmarksPage3->total,
            'Should return total of 25 bookmarks'
        );
    }

    public function testGetUserBookmarksPaginationInvalidPage(): void {
        $user = User::factory()->create();
        Bookmark::factory()->count(15)->create(['user_id' => $user->id]);

        $bookmarks999 = $this->bookmarkService->getUserBookmarks($user->id, 999);
        $bookmarksMinus1 = $this->bookmarkService->getUserBookmarks($user->id, -1);

        $this->assertCount(
            5, $bookmarks999->items,
            'Should return last page for an invalid page number (too large)'
        );

        $this->assertCount(
            10, $bookmarksMinus1->items,
            'Should return first page for an invalid page number (too small)'
        );
    }

    public function testGetUserBookmarksPaginationInvalidPerPage(): void {
        $user = User::factory()->create();
        Bookmark::factory()->count(15)->create(['user_id' => $user->id]);

        $bookmarksZero = $this->bookmarkService->getUserBookmarks($user->id, 1, 0);
        $bookmarksMinus5 = $this->bookmarkService->getUserBookmarks($user->id, 1, -5);
        $bookmarks999 = $this->bookmarkService->getUserBookmarks($user->id, 1, 999);

        $this->assertCount(
            0, $bookmarksZero->items,
            'Should return 0 items for perPage of 0'
        );

        $this->assertCount(
            0, $bookmarksMinus5->items,
            'Should return 0 items for negative perPage'
        );

        $this->assertCount(
            15, $bookmarks999->items,
            'Should return all items if perPage exceeds total'
        );

        $this->assertEquals(
            999, $bookmarks999->perPage,
            'Should reflect requested perPage even if it exceeds total'
        );
    }

    public function testGetUserBookmarksPaginationElementsUniqueness(): void {
        $user = User::factory()->create();
        Bookmark::factory()->count(15)->create(['user_id' => $user->id]);

        $bookmarksPage1 = $this->bookmarkService->getUserBookmarks($user->id, 1, 5);
        $bookmarksPage2 = $this->bookmarkService->getUserBookmarks($user->id, 2, 5);
        $bookmarksPage3 = $this->bookmarkService->getUserBookmarks($user->id, 3, 5);

        $meetIds = [];
        foreach ($bookmarksPage1->items as $bookmark) {
            $this->assertNotContains(
                $bookmark->id, $meetIds,
                'Bookmark ID should be unique across pages'
            );
            $meetIds[] = $bookmark->id;
        }
        foreach ($bookmarksPage2->items as $bookmark) {
            $this->assertNotContains(
                $bookmark->id, $meetIds,
                'Bookmark ID should be unique across pages'
            );
            $meetIds[] = $bookmark->id;
        }
        foreach ($bookmarksPage3->items as $bookmark) {
            $this->assertNotContains(
                $bookmark->id, $meetIds,
                'Bookmark ID should be unique across pages'
            );
            $meetIds[] = $bookmark->id;
        }

        $this->assertCount(
            15, $meetIds,
            'Should have 15 unique bookmark IDs across all pages'
        );
    }

    public function testCreateBookmark(): void {
        $user = User::factory()->create();
        $url = 'https://example.com';
        $title = 'Example Bookmark';

        $bookmark = $this->bookmarkService->createBookmark($user->id, $url, $title);

        $this->assertInstanceOf(Bookmark::class, $bookmark);
        $this->assertEquals($user->id, $bookmark->user_id);
        $this->assertEquals($url, $bookmark->url);
        $this->assertEquals($title, $bookmark->title);
        $this->assertFalse(
            $bookmark->is_read,
            'New bookmark should be marked as unread by default'
        );

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'user_id' => $user->id,
            'url' => $url,
            'title' => $title,
            'is_read' => false,
        ]);
    }

    public function testCreateBookmarkNotExistUser(): void {
        $url = 'https://example.com';
        $title = 'Example Bookmark';

        $this->expectException(QueryException::class);
        $this->bookmarkService->createBookmark(42, $url, $title);
    }

    public function testDeleteUserBookmark(): void {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);

        $result = $this->bookmarkService->deleteUserBookmark(
            $user->id, $bookmark->id
        );

        $this->assertTrue(
            $result, 'Deletion should return true for existing bookmark'
        );

        $this->assertDatabaseMissing('bookmarks', [
            'id' => $bookmark->id,
        ]);
    }

    public function testDeleteUserBookmarkNotExising(): void {
        $user = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $user->id]);

        $result = $this->bookmarkService->deleteUserBookmark(
            $user->id, $bookmark->id + 1
        );

        $this->assertFalse(
            $result, 'Deletion should return false for non-existing bookmark'
        );

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
        ]);

        $this->assertDatabaseMissing('bookmarks', [
            'id' => $bookmark->id + 1,
        ]);
    }

    public function testDeleteUserBookmarkWrongUser(): void {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $bookmark1 = Bookmark::factory()->create(['user_id' => $user1->id]);
        $bookmark2 = Bookmark::factory()->create(['user_id' => $user2->id]);

        $result = $this->bookmarkService->deleteUserBookmark(
            $user2->id, $bookmark1->id
        );

        $this->assertFalse(
            $result,
            'Deletion should return false when user does not own the bookmark'
        );

        $this->assertDatabaseHas('bookmarks', ['id' => $bookmark1->id]);
        $this->assertDatabaseHas('bookmarks', ['id' => $bookmark2->id]);
    }
}
