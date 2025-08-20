@extends('layouts.app')

@section('title', 'Reading List')

@section('content')
    <div class="container mx-auto max-w-2xl px-4 py-8">
        @if($bookmarks->count() > 0)
            <div class="flex flex-col space-y-2">
                @foreach($bookmarks as $bookmark)
                    <div class="bg-white rounded-lg shadow border hover:shadow-md transition-shadow duration-200">
                        <div class="px-3 py-2">
                            <div class="flex sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ $bookmark->url }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="text-blue-600 hover:text-blue-800 font-medium text-lg break-words"
                                       title="{{ $bookmark->title }}"
                                    >
                                        {{ Str::limit($bookmark->title, 35) }}
                                    </a>
                                </div>
                                <div class="flex flex-row gap-3 items-center">
                                    <div>
                                        @if($bookmark->is_read)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Read
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Unread
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500 whitespace-nowrap">
                                        {{ $bookmark->created_at->format('M j, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $bookmarks->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-500 text-lg">No bookmarks found</div>
                <div class="text-gray-400 text-sm mt-2">Add your first bookmark to get started</div>
            </div>
        @endif
    </div>
@endsection
