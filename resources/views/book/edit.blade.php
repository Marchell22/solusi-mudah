@extends('layouts.app')

@section('header', 'Edit Book')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <h1 class="text-2xl font-bold mb-6">Edit Book</h1>

        <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-form-input 
                label="Title" 
                name="title" 
                required="true" 
                :value="$book->title"
            />

            <x-form-input 
                label="Author" 
                name="author" 
                required="true" 
                :value="$book->author"
            />

            <x-form-input 
                label="Description" 
                name="description" 
                type="textarea" 
                :value="$book->description"
            />

            <x-form-input label="Category" name="category_id" type="select" required="true">
                <option value="">Select a Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </x-form-input>

            <x-form-input 
                label="Stock" 
                name="stock" 
                type="number" 
                required="true" 
                :value="$book->stock"
            />

            @if($book->cover_path)
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Current Cover</label>
                    <div class="flex items-center">
                        <a href="{{ Storage::url($book->cover_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            View Current PDF
                        </a>
                    </div>
                </div>
            @endif

            <x-form-input 
                label="Cover (PDF only, 100KB - 500KB)" 
                name="cover" 
                type="file" 
            />

            <x-form-input 
                label="Is Available" 
                name="is_available" 
                type="checkbox" 
                :value="$book->is_available"
            />

            <x-form-input 
                label="Additional Info (JSON)" 
                name="additional_info" 
                type="textarea" 
                :value="json_encode($book->additional_info, JSON_PRETTY_PRINT)"
            />

            <x-form-input 
                label="Published Date" 
                name="published_at" 
                type="date" 
                :value="$book->published_at ? $book->published_at->format('Y-m-d') : ''"
            />

            <div class="flex justify-end mt-6">
                <a href="{{ route('books.show', $book) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </a>
                <x-button type="submit">
                    Update Book
                </x-button>
            </div>
        </form>
    </div>
@endsection