@extends('layouts.app')

@section('title', 'Add Bookmark')

@section('content')
    <div class="min-h-screen flex items-top justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Add New Bookmark
                </h2>
            </div>
            <form class="mt-8 space-y-6" action="{{ route('bookmarks.store') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There were errors with your submission
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="url" class="sr-only">URL</label>
                        <input id="url" name="url" type="url" required autofocus
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('url') border-red-300 @enderror"
                               placeholder="URL" value="{{ old('url') }}">
                    </div>
                    <div>
                        <label for="title" class="sr-only">Title</label>
                        <input id="title" name="title" type="text" required
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('title') border-red-300 @enderror"
                               placeholder="Title" value="{{ old('title') }}">
                    </div>
                </div>

                <div class="flex flex-col space-y-3">
                    <button type="submit" name="action" value="create"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create
                    </button>

                    <button type="submit" name="action" value="create_continue"
                            class="group relative w-full flex justify-center py-2 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create and Continue
                    </button>

                    <a href="javascript:history.back()"
                       class="group relative w-full flex justify-center py-2 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
