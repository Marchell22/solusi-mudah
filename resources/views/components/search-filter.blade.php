<div class="bg-white shadow rounded-lg mb-6 p-4">
    <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap gap-4">
        <div class="flex-grow md:w-1/3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        
        {{ $slot }}
        
        <div class="flex items-center space-x-2">
            <x-button type="submit" color="indigo">Filter</x-button>
            <a href="{{ url()->current() }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:bg-gray-300 transition ease-in-out duration-150">
                Reset
            </a>
        </div>
    </form>
</div>