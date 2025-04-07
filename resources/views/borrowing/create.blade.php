@extends('layouts.app')

@section('header', 'Add Borrowing')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <h1 class="text-2xl font-bold mb-6">Add New Borrowing</h1>

        <form action="{{ route('borrowings.store') }}" method="POST">
            @csrf

            <x-form-input label="Book" name="book_id" type="select" required="true">
                <option value="">Select a Book</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                        {{ $book->title }} ({{ $book->stock }} available)
                    </option>
                @endforeach
            </x-form-input>

            <x-form-input 
                label="Borrower Name" 
                name="borrower_name" 
                required="true" 
            />

            <x-form-input 
                label="Borrower Email" 
                name="borrower_email" 
                type="email"
                required="true" 
            />

            <x-form-input 
                label="Borrow Date" 
                name="borrowed_at" 
                type="date" 
                required="true" 
                value="{{ now()->format('Y-m-d') }}"
            />

            <x-form-input 
                label="Due Date" 
                name="due_date" 
                type="date" 
                required="true" 
                value="{{ now()->addWeeks(2)->format('Y-m-d') }}"
            />

            <x-form-input 
                label="Notes (JSON)" 
                name="notes" 
                type="textarea" 
                value='{"purpose": "research", "condition": "good"}'
            />

            <div class="flex justify-end mt-6">
                <a href="{{ route('borrowings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </a>
                <x-button type="submit">
                    Create Borrowing
                </x-button>
            </div>
        </form>
    </div>
@endsection