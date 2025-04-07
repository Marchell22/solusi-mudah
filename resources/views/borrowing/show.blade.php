
@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Borrowing Details</h1>
            <div>
                <a href="{{ route('borrowings.edit', $borrowing) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Edit
                </a>
                <form action="{{ route('borrowings.destroy', $borrowing) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this borrowing record?')">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-2">Borrowing Information</h2>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <p class="text-gray-600">Borrowing ID:</p>
                            <p class="font-medium">{{ $borrowing->id }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status:</p>
                            <p class="font-medium">
                                @if($borrowing->is_returned)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Returned on {{ $borrowing->returned_at->format('Y-m-d') }}
                                    </span>
                                @elseif($borrowing->due_date < now())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Overdue by {{ now()->diffInDays($borrowing->due_date) }} days
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Active - Due in {{ now()->diffInDays($borrowing->due_date) }} days
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600">Borrower:</p>
                            <p class="font-medium">{{ $borrowing->borrower_name }}</p>
                            <p class="text-sm text-gray-500">{{ $borrowing->borrower_email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Borrow Date:</p>
                            <p class="font-medium">{{ $borrowing->borrowed_at->format('Y-m-d') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Due Date:</p>
                            <p class="font-medium">{{ $borrowing->due_date->format('Y-m-d') }}</p>
                        </div>
                        @if($borrowing->is_returned)
                        <div>
                            <p class="text-gray-600">Return Date:</p>
                            <p class="font-medium">{{ $borrowing->returned_at->format('Y-m-d') }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-gray-600">Created:</p>
                            <p class="font-medium">{{ $borrowing->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                @if($borrowing->notes)
                <div class="mt-4">
                    <h3 class="text-lg font-semibold mb-2">Notes</h3>
                    <div class="bg-gray-100 p-3 rounded">
                        <pre class="text-sm">{{ json_encode($borrowing->notes, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-2">Book Information</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="mb-4">
                        <p class="text-gray-600">Title:</p>
                        <p class="font-medium">
                            <a href="{{ route('books.show', $borrowing->book) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $borrowing->book->title }}
                            </a>
                        </p>
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-600">Author:</p>
                        <p class="font-medium">{{ $borrowing->book->author }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-600">Category:</p>
                        <p class="font-medium">
                            <a href="{{ route('categories.show', $borrowing->book->category) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $borrowing->book->category->name }}
                            </a>
                        </p>
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-600">Current Stock:</p>
                        <p class="font-medium">{{ $borrowing->book->stock }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-600">Status:</p>
                        <p class="font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $borrowing->book->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $borrowing->book->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        </p>
                    </div>
                    @if($borrowing->book->cover_path)
                    <div class="mt-4">
                        <a href="{{ Storage::url($borrowing->book->cover_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            View Book Cover
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('borrowings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Borrowings
            </a>
        </div>
    </div>
@endsection
