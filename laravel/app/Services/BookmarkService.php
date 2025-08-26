<?php

namespace App\Services;

use App\DTO\PaginatedResult;
use App\Models\Bookmark;

class BookmarkService
{
    /**
     * Get paginated bookmarks for a user.
     *
     * @return PaginatedResult<Bookmark>
     */
    public function getUserBookmarks(int $userId, int $page = 1, int $perPage = 10): PaginatedResult
    {
        $query = Bookmark::query()
            ->where('user_id', $userId);

        $total = $query->count();

        if ($total === 0 || $perPage < 1) {
            return new PaginatedResult([], 0, $perPage, $page);
        }

        if ($page < 1) {
            $page = 1;
        }

        $maxPage = (int) ceil($total / $perPage);
        if ($page > $maxPage) {
            $page = $maxPage;
        }

        $items = $query
            ->orderByDesc('created_at')
            ->forPage($page, $perPage)
            ->getModels();

        return new PaginatedResult(
            items: $items,
            total: $total,
            perPage: $perPage,
            currentPage: $page,
        );
    }

    /**
     * Create a new bookmark for a user.
     *
     * @th
     */
    public function createBookmark(int $userId, string $url, string $title): Bookmark
    {
        return Bookmark::query()->create([
            'user_id' => $userId,
            'url' => $url,
            'title' => $title,
            'is_read' => false,
        ]);
    }

    public function deleteUserBookmark(int $userId, int $bookmarkId): bool
    {
        return Bookmark::query()
            ->where('user_id', $userId)
            ->where('id', $bookmarkId)
            ->delete() > 0;
    }
}
