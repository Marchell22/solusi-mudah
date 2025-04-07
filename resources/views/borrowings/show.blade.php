<x-app-layout>
    <x-slot:header>
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Borrowing Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Borrowing Details') }}
                        </h2>
                        <div>
                            <a href="{{ route('borrowings.edit', $borrowing) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none transition ease-in-out duration-150 mr-2">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('borrowings.destroy', $borrowing) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this borrowing record?')">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-base font-medium text-gray-900 mb-2">Borrowing Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Borrowing ID:</p>
                                        <p class="font-medium text-gray-900">{{ $borrowing->id }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Status:</p>
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
                                        <p class="text-sm text-gray-600">Borrower:</p>
                                        <p class="font-medium text-gray-900">{{ $borrowing->borrower_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $borrowing->borrower_email }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Borrow Date:</p>
                                        <p class="font-medium text-gray-900">{{ $borrowing->borrowed_at->format('Y-m-d') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Due Date:</p>
                                        <p class="font-medium text-gray-900">{{ $borrowing->due_date->format('Y-m-d') }}</p>
                                    </div>
                                    @if($borrowing->is_returned)
                                    <div>
                                        <p class="text-sm text-gray-600">Return Date:</p>
                                        <p class="font-medium text-gray-900">{{ $borrowing->returned_at->format('Y-m-d') }}</p>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="text-sm text-gray-600">Created:</p>
                                        <p class="font-medium text-gray-900">{{ $borrowing->created_at->format('Y-m-d H:i:s') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($borrowing->notes)
                            <div class="mt-4">
                                <h3 class="text-base font-medium text-gray-900 mb-2">Notes</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <pre class="text-sm text-gray-700">{{ json_encode($borrowing->notes, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-base font-medium text-gray-900 mb-2">Book Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">Title:</p>
                                    <p class="font-medium text-gray-900">
                                        <a href="{{ route('books.show', $borrowing->book) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $borrowing->book->title }}
                                        </a>
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">Author:</p>
                                    <p class="font-medium text-gray-900">{{ $borrowing->book->author }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">Category:</p>
                                    <p class="font-medium text-gray-900">
                                        <a href="{{ route('categories.show', $borrowing->book->category) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $borrowing->book->category->name }}
                                        </a>
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">Current Stock:</p>
                                    <p class="font-medium text-gray-900">{{ $borrowing->book->stock }}</p>
                                </div>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">Status:</p>
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
                        <a href="{{ route('borrowings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:bg-gray-300 transition ease-in-out duration-150">
                            {{ __('Back to Borrowings') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>