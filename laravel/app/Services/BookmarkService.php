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
}
