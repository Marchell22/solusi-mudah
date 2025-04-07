<x-app-layout>
    <x-slot:header>
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Import '.ucfirst($type)) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">
                        {{ __('Upload File for Import') }}
                    </h2>

                    <div class="mb-6">
                        <p class="mb-2 text-gray-600">Download an import template to get started:</p>
                        <a href="{{ route('excel.template', $type) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none transition ease-in-out duration-150">
                            {{ __('Download Template') }}
                        </a>
                    </div>

                    <form action="{{ route('excel.import', $type) }}" method="POST" enctype="multipart/form-data" class="mb-6">
                        @csrf

                        <!-- File Upload -->
                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                Excel File <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="file" 
                                id="file" 
                                name="file"
                                class="w-full text-sm text-gray-500 border border-gray-300 rounded-md shadow-sm"
                                required
                                accept=".xlsx,.xls,.csv"
                            >
                            @error('file')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Supported formats: XLSX, XLS, CSV</p>
                        </div>

                        <!-- Field Mapping -->
                        <div class="mb-6">
                            <h3 class="text-base font-medium text-gray-900 mb-2">Field Mapping</h3>
                            <p class="mb-4 text-gray-600">Map your Excel columns to database fields:</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($fieldMaps as $dbField => $label)
                                    <div class="mb-2">
                                        <label for="field_{{ $dbField }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ $label }}
                                        </label>
                                        <input 
                                            type="text" 
                                            id="field_{{ $dbField }}" 
                                            name="field_map[{{ $dbField }}]" 
                                            placeholder="Excel Column Name"
                                            value="{{ old('field_map.'.$dbField, $dbField) }}"
                                            class="rounded-md border-gray-300 shadow-sm w-full"
                                        >
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-between">
                            <a href="{{ route($type.'.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:bg-gray-300 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">
                                {{ __('Import Data') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>