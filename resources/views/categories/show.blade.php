<x-app-layout>
    <x-slot:header>
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Category Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ $category->name }}
                        </h2>
                        <div>
                            <a href="{{ route('categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none transition ease-in-out duration-150 mr-2">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this category?')">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-base font-medium text-gray-900 mb-2">Category Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">ID:</p>
                                    <p class="font-medium text-gray-900">{{ $category->id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Name:</p>
                                    <p class="font-medium text-gray-900">{{ $category->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status:</p>
                                    <p class="font-medium">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Created At:</p>
                                    <p class="font-medium text-gray-900">{{ $category->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-base font-medium text-gray-900 mb-2">Description</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700">{{ $category->description ?: 'No description available' }}</p>
                        </div>
                    </div>

                    @if($category->metadata)
                    <div class="mb-6">
                        <h3 class="text-base font-medium text-gray-900 mb-2">Metadata</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-700">{{ json_encode($category->metadata, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-base font-medium text-gray-900 mb-2">Books in this Category ({{ $category->books->count() }})</h3>
                        @if($category->books->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($category->books as $book)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="font-medium text-gray-900">{{ $book->title }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-500">{{ $book->author }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-500">{{ $book->stock }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $book->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $book->is_available ? 'Available' : 'Unavailable' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('books.show', $book) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-500">No books in this category yet.</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:bg-gray-300 transition ease-in-out duration-150">
                            {{ __('Back to Categories') }}
                        </a>
                    </div>
                    <x-audit-trail :model="$category" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>