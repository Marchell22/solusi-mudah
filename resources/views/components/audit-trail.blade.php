<!-- resources/views/components/audit-trail.blade.php -->
@props(['model'])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
    <div class="p-6 bg-white border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Audit Trail</h3>
        
        @if($model->audits->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Event
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Changes
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date & Time
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($model->audits->sortByDesc('created_at') as $audit)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($audit->event == 'created') bg-green-100 text-green-800 
                                        @elseif($audit->event == 'updated') bg-blue-100 text-blue-800 
                                        @elseif($audit->event == 'deleted') bg-red-100 text-red-800 
                                        @else bg-yellow-100 text-yellow-800 
                                        @endif">
                                        {{ ucfirst($audit->event) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($audit->event == 'created')
                                        <div class="text-sm text-gray-900">New Record Created</div>
                                        @if(count($audit->new_values) > 0)
                                            <div class="mt-1 text-xs text-gray-500">
                                                <details>
                                                    <summary>View Details</summary>
                                                    <div class="mt-2 space-y-1">
                                                        @foreach($audit->new_values as $key => $value)
                                                            <div><strong>{{ ucfirst($key) }}:</strong> 
                                                                @if(is_array($value))
                                                                    <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                @else
                                                                    {{ $value }}
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </details>
                                            </div>
                                        @endif
                                    @elseif($audit->event == 'updated')
                                        <div class="text-sm text-gray-900">Record Updated</div>
                                        @if(count($audit->old_values) > 0 || count($audit->new_values) > 0)
                                            <div class="mt-1 text-xs text-gray-500">
                                                <details>
                                                    <summary>View Changes</summary>
                                                    <div class="mt-2 space-y-1">
                                                        @foreach($audit->new_values as $key => $value)
                                                            <div>
                                                                <strong>{{ ucfirst($key) }}:</strong> 
                                                                <span class="line-through text-red-500">
                                                                    @if(isset($audit->old_values[$key]))
                                                                        @if(is_array($audit->old_values[$key]))
                                                                            <pre>{{ json_encode($audit->old_values[$key], JSON_PRETTY_PRINT) }}</pre>
                                                                        @else
                                                                            {{ $audit->old_values[$key] }}
                                                                        @endif
                                                                    @endif
                                                                </span> → 
                                                                <span class="text-green-500">
                                                                    @if(is_array($value))
                                                                        <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </details>
                                            </div>
                                        @endif
                                    @elseif($audit->event == 'deleted')
                                        <div class="text-sm text-gray-900">Record Deleted</div>
                                    @elseif($audit->event == 'restored')
                                        <div class="text-sm text-gray-900">Record Restored</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($audit->user)
                                            {{ $audit->user->name }}
                                        @else
                                            System
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $audit->ip_address }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $audit->created_at->format('Y-m-d H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No audit records found.</p>
        @endif
    </div>
</div>