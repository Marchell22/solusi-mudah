@extends('layouts.app')

@section('header', 'Add Book')

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <h1 class="text-2xl font-bold mb-6">Add New Book</h1>

        <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <x-form-input 
                label="Title" 
                name="title" 
                required="true" 
            />

            <x-form-input 
                label="Author" 
                name="author" 
                required="true" 
            />

            <x-form-input 
                label="Description" 
                name="description" 
                type="textarea" 
            />

            <x-form-input label="Category" name="category_id" type="select" required="true">
                <option value="">Select a Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </x-form-input>

            <x-form-input 
                label="Stock" 
                name="stock" 
                type="number" 
                required="true" 
                value="1"
            />

            <x-form-input 
                label="Cover (PDF only, 100KB - 500KB)" 
                name="cover" 
                type="file" 
            />

            <x-form-input 
                label="Is Available" 
                name="is_available" 
                type="checkbox" 
                value="1" 
            />

            <x-form-input 
                label="Additional Info (JSON)" 
                name="additional_info" 
                type="textarea" 
                value='{"key": "value"}' 
            />

            <x-form-input 
                label="Published Date" 
                name="published_at" 
                type="date" 
            />

            <div class="flex justify-end mt-6">
                <a href="{{ route('books.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </a>
                <x-button type="submit">
                    Create Book
                </x-button>
            </div>
        </form>
    </div>
@endsection