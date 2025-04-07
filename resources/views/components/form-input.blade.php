@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => false])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-gray-700 text-sm font-bold mb-2">
        {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
    </label>
    
    @if($type === 'textarea')
        <textarea 
            id="{{ $name }}" 
            name="{{ $name }}" 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error($name) border-red-500 @enderror"
            {{ $required ? 'required' : '' }}
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select 
            id="{{ $name }}" 
            name="{{ $name }}" 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error($name) border-red-500 @enderror"
            {{ $required ? 'required' : '' }}
        >
            {{ $slot }}
        </select>
    @elseif($type === 'checkbox')
        <div class="mt-2">
            <input 
                type="checkbox" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                {{ old($name, $value) ? 'checked' : '' }}
            >
        </div>
    @elseif($type === 'file')
        <input 
            type="file" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error($name) border-red-500 @enderror"
            {{ $required ? 'required' : '' }}
        >
    @elseif($type === 'date')
        <input 
            type="date" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error($name) border-red-500 @enderror"
            {{ $required ? 'required' : '' }}
        >
    @elseif($type === 'datetime-local')
        <input 
            type="datetime-local" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error($name) border-red-500 @enderror"
            {{ $required ? 'required' : '' }}
        >
    @else
        <input 
            type="{{ $type }}" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error($name) border-red-500 @enderror"
            {{ $required ? 'required' : '' }}
        >
    @endif
    
    @error($name)
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>