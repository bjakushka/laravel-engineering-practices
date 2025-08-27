<?php

namespace App\Http\Controllers;

use App\Http\Pagination\PaginatorFactory;
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
        $bookmarksPaginated = $this->bookmarkService->getUserBookmarks(
            (int) auth()->id(),
            $request->integer('page', 1),
            $request->integer('per_page', 10)
        );

        return view('bookmarks.index', [
            'bookmarks' => PaginatorFactory::fromPaginatedResult(
                $bookmarksPaginated, $request
            )
        ]);
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
            'action' => ['string', 'in:create,create_continue', 'sometimes'],
        ]);

        $this->bookmarkService->createBookmark(
            (int) auth()->id(),
            (string) $request->input('url'),
            (string) $request->input('title')
        );

        $action = (string) $request->input('action');

        if ($action === 'create_continue') {
            return redirect()->route('bookmarks.create')
                ->with('success', 'bookmark created successfully');
        }

        return redirect()
            ->route('index')
            ->with('success', 'bookmark created successfully');
    }

    public function destroy(int $id): RedirectResponse
    {
        $result = $this->bookmarkService->deleteUserBookmark(
            (int) auth()->id(), $id
        );

        if (!$result) {
            return redirect()->route('index')->with(
                'error', 'bookmark not found or could not be deleted'
            );
        }

        return redirect()->route('index')
            ->with('success', 'bookmark deleted successfully');
    }
}
