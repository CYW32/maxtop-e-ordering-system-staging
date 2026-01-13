<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Activity Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- THE DYNAMIC SEARCH TOOLBAR [4] --}}
                <div class="mb-6">
                    <x-filter-toolbar :placeholder="__('Search Name or Login ID...')">
                        {{-- Action Type Dropdown --}}
                        <select name="action_type"
                            class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('All Actions') }}</option>
                            @foreach ($actionTypes as $type)
                                <option value="{{ $type }}"
                                    {{ request('action_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Role Dropdown --}}
                        <select name="role"
                            class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('All Roles') }}</option>
                            @foreach ($roles as $roleName)
                                <option value="{{ $roleName }}"
                                    {{ request('role') == $roleName ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $roleName)) }}
                                </option>
                            @endforeach
                        </select>
                    </x-filter-toolbar>
                </div>

                {{-- LOG TABLE --}}
                {{-- LOG TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Time</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User (Causer)</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Target (Subject)</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Changes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($logs as $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $log->causer->name ?? 'System' }}
                                        <span
                                            class="text-xs text-gray-400">({{ $log->causer->login_id ?? 'N/A' }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $log->description === 'updated' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($log->description) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ class_basename($log->subject_type) }} (ID: {{ $log->subject_id }})
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{-- Displaying Old vs New values from the 'properties' JSON --}}
                                        @if (isset($log->properties['attributes']))
                                            <div class="text-xs">
                                                <strong>New:</strong>
                                                @foreach ($log->properties['attributes'] as $key => $value)
                                                    {{ $key }}:
                                                    {{ is_array($value) ? json_encode($value) : $value }},
                                                @endforeach
                                            </div>
                                        @endif
                                        @if (isset($log->properties['old']))
                                            <div class="text-xs mt-1 text-red-400">
                                                <strong>Old:</strong>
                                                @foreach ($log->properties['old'] as $key => $value)
                                                    {{ $key }}:
                                                    {{ is_array($value) ? json_encode($value) : $value }},
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
