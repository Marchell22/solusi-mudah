@extends('layouts.app')

@section('header', 'Book Details')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">{{ $book->title }}</h1>
            <div>
                <a href="{{ route('books.edit', $book) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Edit
                </a>
                <form action="{{ route('books.destroy', $book) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this book?')">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-2">Book Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">ID:</p>
                            <p class="font-medium">{{ $book->id }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Title:</p>
                            <p class="font-medium">{{ $book->title }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Author:</p>
                            <p class="font-medium">{{ $book->author }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Category:</p>
                            <p class="font-medium">
                                <a href="{{ route('categories.show', $book->category) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $book->category->name }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600">Stock:</p>
                            <p class="font-medium">{{ $book->stock }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status:</p>
                            <p class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $book->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $book->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600">Published Date:</p>
                            <p class="font-medium">{{ $book->published_at ? $book->published_at->format('Y-m-d') : 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Created At:</p>
                            <p class="font-medium">{{ $book->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Description</h3>
                    <p class="text-gray-700">{{ $book->description ?: 'No description available' }}</p>
                </div>

                @if($book->additional_info)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Additional Information</h3>
                    <div class="bg-gray-100 p-3 rounded">
                        <pre class="text-sm">{{ json_encode($book->additional_info, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
            </div>

            <div>
                @if($book->cover_path)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Cover</h3>
                    <div class="border rounded p-2">
                        <a href="{{ Storage::url($book->cover_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            View PDF
                        </a>
                    </div>
                </div>
                @endif

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Borrowing History</h3>
                    @if($book->borrowings->count() > 0)
                        <div class="space-y-2">
                            @foreach($book->borrowings->sortByDesc('borrowed_at')->take(5) as $borrowing)
                                <div class="border rounded p-2 {{ $borrowing->is_returned ? 'bg-green-50' : ($borrowing->due_date < now() ? 'bg-red-50' : 'bg-yellow-50') }}">
                                    <p class="font-medium">{{ $borrowing->borrower_name }}</p>
                                    <p class="text-sm text-gray-600">
                                        Borrowed: {{ $borrowing->borrowed_at->format('Y-m-d') }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Due: {{ $borrowing->due_date->format('Y-m-d') }}
                                    </p>
                                    <p class="text-sm font-medium {{ $borrowing->is_returned ? 'text-green-700' : ($borrowing->due_date < now() ? 'text-red-700' : 'text-yellow-700') }}">
                                        @if($borrowing->is_returned)
                                            Returned on {{ $borrowing->returned_at->format('Y-m-d') }}
                                        @elseif($borrowing->due_date < now())
                                            Overdue by {{ now()->diffInDays($borrowing->due_date) }} days
                                        @else
                                            Due in {{ now()->diffInDays($borrowing->due_date) }} days
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        @if($book->borrowings->count() > 5)
                            <p class="mt-2 text-sm text-gray-500">
                                Showing 5 of {{ $book->borrowings->count() }} borrowings.
                                <a href="{{ route('borrowings.index', ['book' => $book->id]) }}" class="text-indigo-600 hover:text-indigo-900">See all</a>
                            </p>
                        @endif
                    @else
                        <p class="text-gray-500">This book has never been borrowed.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('books.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Books
            </a>
        </div>
    </div>
@endsection
