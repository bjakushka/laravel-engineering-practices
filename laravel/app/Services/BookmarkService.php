<?php

namespace App\Services;

use App\Models\Bookmark;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookmarkService
{
    public function getUserBookmarks(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Bookmark::query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

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
