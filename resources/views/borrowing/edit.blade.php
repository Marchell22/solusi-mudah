@extends('layouts.app')

@section('header', 'Edit Borrowing')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <h1 class="text-2xl font-bold mb-6">Edit Borrowing</h1>

        <form action="{{ route('borrowings.update', $borrowing) }}" method="POST">
            @csrf
            @method('PUT')

            <x-form-input label="Book" name="book_id" type="select" required="true">
                <option value="">Select a Book</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}" {{ old('book_id', $borrowing->book_id) == $book->id ? 'selected' : '' }}>
                        {{ $book->title }}
                    </option>
                @endforeach
            </x-form-input>

            <x-form-input 
                label="Borrower Name" 
                name="borrower_name" 
                required="true" 
                :value="$borrowing->borrower_name"
            />

            <x-form-input 
                label="Borrower Email" 
                name="borrower_email" 
                type="email"
                required="true" 
                :value="$borrowing->borrower_email"
            />

            <x-form-input 
                label="Borrow Date" 
                name="borrowed_at" 
                type="date" 
                required="true" 
                :value="$borrowing->borrowed_at->format('Y-m-d')"
            />

            <x-form-input 
                label="Due Date" 
                name="due_date" 
                type="date" 
                required="true" 
                :value="$borrowing->due_date->format('Y-m-d')"
            />

            <x-form-input 
                label="Return Date" 
                name="returned_at" 
                type="date" 
                :value="$borrowing->returned_at ? $borrowing->returned_at->format('Y-m-d') : ''"
            />

            <x-form-input 
                label="Is Returned" 
                name="is_returned" 
                type="checkbox" 
                :value="$borrowing->is_returned"
            />

            <x-form-input 
                label="Notes (JSON)" 
                name="notes" 
                type="textarea" 
                :value="json_encode($borrowing->notes, JSON_PRETTY_PRINT)"
            />

            <div class="flex justify-end mt-6">
                <a href="{{ route('borrowings.show', $borrowing) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </a>
                <x-button type="submit">
                    Update Borrowing
                </x-button>
            </div>
        </form>
    </div>
@endsection