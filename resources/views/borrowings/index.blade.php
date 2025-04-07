<!-- resources/views/borrowings/index.blade.php -->
<x-app-layout>
    <x-slot:header>
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Borrowings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Borrowings List') }}
                        </h2>
                        <a href="{{ route('borrowings.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">
                            {{ __('Add Borrowing') }}
                        </a>
                    </div>

                    <!-- Search & Filter -->
                    <div class="bg-white shadow rounded-lg mb-6 p-4">
                        <form method="GET" action="{{ route('borrowings.index') }}" class="flex flex-wrap gap-4">
                            <div class="flex-grow md:w-1/3">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-md border-gray-300 shadow-sm w-full">
                            </div>
                            
                            <div>
                                <select name="book" class="rounded-md border-gray-300 shadow-sm w-full">
                                    <option value="">All Books</option>
                                    @foreach($books as $book)
                                        <option value="{{ $book->id }}" {{ request('book') == $book->id ? 'selected' : '' }}>
                                            {{ $book->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="status" class="rounded-md border-gray-300 shadow-sm w-full">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                            <div>
                                <select name="sort_by" class="rounded-md border-gray-300 shadow-sm w-full">
                                    <option value="borrowed_at" {{ request('sort_by') === 'borrowed_at' ? 'selected' : '' }}>Sort by Borrow Date</option>
                                    <option value="due_date" {{ request('sort_by') === 'due_date' ? 'selected' : '' }}>Sort by Due Date</option>
                                    <option value="borrower_name" {{ request('sort_by') === 'borrower_name' ? 'selected' : '' }}>Sort by Borrower</option>
                                </select>
                            </div>
                            <div>
                                <select name="sort_order" class="rounded-md border-gray-300 shadow-sm w-full">
                                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                                </select>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">Filter</button>
                                <a href="{{ route('borrowings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:bg-gray-300 transition ease-in-out duration-150">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrower</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($borrowings as $borrowing)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <!-- Tampilkan data immutable jika ada, jika tidak gunakan relasi book -->
                                            <div class="font-medium text-gray-900">
                                                <a href="{{ route('books.show', $borrowing->book) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $borrowing->book_title ?: $borrowing->book->title }}
                                                </a>
                                                
                                                <!-- Tampilkan indikator jika data buku telah berubah -->
                                                @if(
                                                    $borrowing->book_title && 
                                                    $borrowing->book_title !== $borrowing->book->title
                                                )
                                                    <span class="inline-flex items-center px-2 py-0.5 ml-2 rounded text-xs bg-yellow-100 text-yellow-800">
                                                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                        Changed
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $borrowing->book_author ?: $borrowing->book->author }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ $borrowing->borrower_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $borrowing->borrower_email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $borrowing->borrowed_at->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $borrowing->due_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($borrowing->is_returned)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Returned
                                                </span>
                                            @elseif($borrowing->due_date < now())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Overdue
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Active
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('borrowings.show', $borrowing) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a>
                                            <a href="{{ route('borrowings.edit', $borrowing) }}" class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</a>
                                            <form action="{{ route('borrowings.destroy', $borrowing) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this borrowing record?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center">
                                            <div class="text-gray-500">No borrowings found</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $borrowings->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>