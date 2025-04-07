<x-app-layout>
    <x-slot:header>
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Create Book') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">
                        {{ __('Add New Book') }}
                    </h2>

                    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                value="{{ old('title') }}"
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                required
                            >
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Author -->
                        <div class="mb-4">
                            <label for="author" class="block text-sm font-medium text-gray-700 mb-2">
                                Author <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="author" 
                                name="author" 
                                value="{{ old('author') }}"
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                required
                            >
                            @error('author')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                rows="4"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="category_id" 
                                name="category_id" 
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                required
                            >
                                <option value="">Select a Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div class="mb-4">
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                                Stock <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="stock" 
                                name="stock" 
                                value="{{ old('stock', 1) }}"
                                min="0"
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                required
                            >
                            @error('stock')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cover -->
                        <div class="mb-4">
                            <label for="cover" class="block text-sm font-medium text-gray-700 mb-2">
                                Cover (PDF only, 100KB - 500KB)
                            </label>
                            <input 
                                type="file" 
                                id="cover" 
                                name="cover" 
                                class="w-full text-sm text-gray-500 border border-gray-300 rounded-md shadow-sm"
                            >
                            @error('cover')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Available -->
                        <div class="mb-4">
                            <label for="is_available" class="block text-sm font-medium text-gray-700 mb-2">
                                Is Available
                            </label>
                            <div class="mt-2">
                                <input 
                                    type="checkbox" 
                                    id="is_available" 
                                    name="is_available" 
                                    value="1"
                                    class="rounded border-gray-300"
                                    {{ old('is_available', 1) ? 'checked' : '' }}
                                >
                            </div>
                            @error('is_available')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Additional Info -->
                        <div class="mb-4">
                            <label for="additional_info" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Info (JSON)
                            </label>
                            <textarea 
                                id="additional_info" 
                                name="additional_info" 
                                class="rounded-md border-gray-300 shadow-sm w-full"
                                rows="3"
                            >{{ old('additional_info', '{"key": "value"}') }}</textarea>
                            @error('additional_info')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Published Date -->
                        <div class="mb-4">
                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Published Date
                            </label>
                            <input 
                                type="date" 
                                id="published_at" 
                                name="published_at" 
                                value="{{ old('published_at') }}"
                                class="rounded-md border-gray-300 shadow-sm w-full"
                            >
                            @error('published_at')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end mt-6">
                            <a href="{{ route('books.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:bg-gray-300 transition ease-in-out duration-150 mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">
                                {{ __('Create Book') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>