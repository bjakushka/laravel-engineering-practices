<?php

namespace App\Http\Controllers;

use App\Services\BookmarkService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

    public function create(): View
    {
        return view('bookmarks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'url' => ['required', 'url'],
            'title' => ['required', 'string', 'max:255'],
        ]);

        $this->bookmarkService->createBookmark(
            auth()->id(),
            $request->input('url'),
            $request->input('title')
        );

        $action = $request->input('action');

        if ($action === 'create_continue') {
            return redirect()->route('bookmarks.create')
                ->with('success', 'bookmark created successfully');
        }

        return redirect()
            ->route('index')
            ->with('success', 'bookmark created successfully');
    }
}
