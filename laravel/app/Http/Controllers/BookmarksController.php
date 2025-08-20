<?php

namespace App\Http\Controllers;

use App\Services\BookmarkService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookmarksController extends Controller
{
    private BookmarkService $bookmarkService;

    public function __construct(BookmarkService $bookmarkService)
    {
        $this->bookmarkService = $bookmarkService;
    }

    public function index(Request $request): View
    {
        $bookmarks = $this->bookmarkService->getUserBookmarks(
            auth()->id(),
            $request->get('per_page', 10)
        );

        return view('bookmarks.index', compact('bookmarks'));
    }
}
