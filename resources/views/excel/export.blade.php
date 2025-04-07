<x-app-layout>
    <x-slot:header>
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Export '.ucfirst($type)) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">
                        {{ __('Select Fields to Export') }}
                    </h2>

                    <form action="{{ route('excel.export', $type) }}" method="POST" class="mb-6">
                        @csrf

                        <div class="mb-6">
                            <p class="mb-4 text-gray-600">Select the fields you want to include in the export file:</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach ($fields as $field => $label)
                                    <div class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            id="field_{{ $field }}" 
                                            name="columns[]" 
                                            value="{{ $field }}"
                                            class="rounded border-gray-300"
                                            @if (old('columns') && in_array($field, old('columns'))) checked @endif
                                        >
                                        <label for="field_{{ $field }}" class="ml-2 block text-sm text-gray-700">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('columns')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8 flex items-center justify-between">
                            <a href="{{ route($type.'.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:bg-gray-300 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                            <div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">
                                    {{ __('Export') }}
                                </button>
                                <button type="submit" name="direct" value="1" class="ml-2 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none transition ease-in-out duration-150">
                                    {{ __('Export Now (Direct Download)') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>