<x-app-layout>
    <x-slot:header>
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Book Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ $book->title }}
                        </h2>
                        <div>
                            <a href="{{ route('books.edit', $book) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none transition ease-in-out duration-150 mr-2">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('books.destroy', $book) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this book?')">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <div class="mb-6">
                                <h3 class="text-base font-medium text-gray-900 mb-2">Book Information</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600">ID:</p>
                                            <p class="font-medium text-gray-900">{{ $book->id }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Title:</p>
                                            <p class="font-medium text-gray-900">{{ $book->title }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Author:</p>
                                            <p class="font-medium text-gray-900">{{ $book->author }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Category:</p>
                                            <p class="font-medium text-gray-900">
                                                <a href="{{ route('categories.show', $book->category) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $book->category->name }}
                                                </a>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Stock:</p>
                                            <p class="font-medium text-gray-900">{{ $book->stock }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Status:</p>
                                            <p class="font-medium">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $book->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $book->is_available ? 'Available' : 'Unavailable' }}
                                                </span>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Published Date:</p>
                                            <p class="font-medium text-gray-900">{{ $book->published_at ? $book->published_at->format('Y-m-d') : 'Not specified' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Created At:</p>
                                            <p class="font-medium text-gray-900">{{ $book->created_at->format('Y-m-d H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <h3 class="text-base font-medium text-gray-900 mb-2">Description</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700">{{ $book->description ?: 'No description available' }}</p>
                                </div>
                            </div>

                            @if($book->additional_info)
                            <div class="mb-6">
                                <h3 class="text-base font-medium text-gray-900 mb-2">Additional Information</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <pre class="text-sm text-gray-700">{{ json_encode($book->additional_info, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div>
                            @if($book->cover_path)
                            <div class="mb-6">
                                <h3 class="text-base font-medium text-gray-900 mb-2">Cover</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
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
                                <h3 class="text-base font-medium text-gray-900 mb-2">Borrowing History</h3>
                                @if($book->borrowings->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($book->borrowings->sortByDesc('borrowed_at')->take(5) as $borrowing)
                                            <div class="border rounded p-3 {{ $borrowing->is_returned ? 'bg-green-50' : ($borrowing->due_date < now() ? 'bg-red-50' : 'bg-yellow-50') }}">
                                                <p class="font-medium text-gray-900">{{ $borrowing->borrower_name }}</p>
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
                                        
                                        @if($book->borrowings->count() > 5)
                                            <p class="mt-2 text-sm text-gray-500">
                                                Showing 5 of {{ $book->borrowings->count() }} borrowings.
                                                <a href="{{ route('borrowings.index', ['book' => $book->id]) }}" class="text-indigo-600 hover:text-indigo-900">See all</a>
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-500">This book has never been borrowed.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:bg-gray-300 transition ease-in-out duration-150">
                            {{ __('Back to Books') }}
                        </a>
                    </div>
                    <x-audit-trail :model="$book" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>