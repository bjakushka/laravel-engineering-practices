<?php

namespace Tests\Feature\Database\Factories;

use App\Models\Bookmark;
use App\Models\User;
use Database\Factories\BookmarkFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(BookmarkFactory::class)]
class BookmarkFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatesValidBookmark(): void
    {
        $bookmark = Bookmark::factory()->create();

        $this->assertInstanceOf(Bookmark::class, $bookmark);
        $this->assertNotNull($bookmark->id);
        $this->assertDatabaseHas('bookmarks', ['id' => $bookmark->id]);

        if ($bookmark->is_read) {
            $this->assertNotNull(
                $bookmark->read_at,
                'read_at should not be null if is_read is true',
            );
        } else {
            $this->assertNull(
                $bookmark->read_at,
                'read_at should be null if is_read is false',
            );
        }
    }

    public function testCreatesReadBookmark(): void
    {
        $bookmark = Bookmark::factory()->create([
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->assertTrue($bookmark->is_read);
        $this->assertNotNull($bookmark->read_at);
        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'is_read' => true,
        ]);
    }

    public function testCreatesBookmarkWithSpecificAttributes(): void
    {
        $attributes = [
            'url' => 'https://example.com',
            'title' => 'Example Bookmark',
        ];

        $bookmark = Bookmark::factory()->create($attributes);

        $this->assertEquals('https://example.com', $bookmark->url);
        $this->assertEquals('Example Bookmark', $bookmark->title);
        $this->assertDatabaseHas('bookmarks', $attributes);
    }

    public function testCreatesBookmarkUserRelation(): void
    {
        $bookmark = Bookmark::factory()->create();
        $this->assertInstanceOf(User::class, $bookmark->user);
    }
}
