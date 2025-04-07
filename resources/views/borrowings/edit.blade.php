<x-app-layout>
    <x-slot:header>
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Edit Borrowing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">
                        {{ __('Edit Borrowing') }}
                    </h2>

                    <form action="{{ route('borrowings.update', $borrowing) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Book -->
                        <div class="mb-4">
                            <label for="book_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Book <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="book_id" 
                                name="book_id" 
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                required
                            >
                                <option value="">Select a Book</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}" {{ old('book_id', $borrowing->book_id) == $book->id ? 'selected' : '' }}>
                                        {{ $book->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('book_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Borrower Name -->
                        <div class="mb-4">
                            <label for="borrower_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Borrower Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="borrower_name" 
                                name="borrower_name" 
                                value="{{ old('borrower_name', $borrowing->borrower_name) }}"
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                required
                            >
                            @error('borrower_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Borrower Email -->
                        <div class="mb-4">
                            <label for="borrower_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Borrower Email <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="borrower_email" 
                                name="borrower_email" 
                                value="{{ old('borrower_email', $borrowing->borrower_email) }}"
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                required
                            >
                            @error('borrower_email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Borrowed At -->
                        <div class="mb-4">
                            <label for="borrowed_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Borrow Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="borrowed_at" 
                                name="borrowed_at" 
                                value="{{ old('borrowed_at', $borrowing->borrowed_at->format('Y-m-d')) }}"
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                required
                            >
                            @error('borrowed_at')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Due Date -->
                        <div class="mb-4">
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Due Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="due_date" 
                                name="due_date" 
                                value="{{ old('due_date', $borrowing->due_date->format('Y-m-d')) }}"
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                required
                            >
                            @error('due_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Returned At -->
                        <div class="mb-4">
                            <label for="returned_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Return Date
                            </label>
                            <input 
                                type="date" 
                                id="returned_at" 
                                name="returned_at" 
                                value="{{ old('returned_at', $borrowing->returned_at ? $borrowing->returned_at->format('Y-m-d') : '') }}"
                                class="rounded-md border-gray-300 shadow-sm w-full"
                            >
                            @error('returned_at')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Returned -->
                        <div class="mb-4">
                            <label for="is_returned" class="block text-sm font-medium text-gray-700 mb-2">
                                Is Returned
                            </label>
                            <div class="mt-2">
                                <input 
                                    type="checkbox" 
                                    id="is_returned" 
                                    name="is_returned" 
                                    value="1"
                                    class="rounded border-gray-300"
                                    {{ old('is_returned', $borrowing->is_returned) ? 'checked' : '' }}
                                >
                            </div>
                            @error('is_returned')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes (JSON)
                            </label>
                            <textarea 
                                id="notes" 
                                name="notes" 
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                rows="3"
                            >{{ old('notes', json_encode($borrowing->notes, JSON_PRETTY_PRINT)) }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end mt-6">
                            <a href="{{ route('borrowings.show', $borrowing) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:bg-gray-300 transition ease-in-out duration-150 mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">
                                {{ __('Update Borrowing') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>