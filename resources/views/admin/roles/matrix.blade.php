<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Feature Access Control') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-4">Permission Matrix</h3>
                <p class="text-sm text-gray-500 mb-6">Check the box to enable a feature for a specific role.</p>

                <form method="POST" action="{{ route('roles.update') }}">
                    @csrf

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    {{-- Corner Cell --}}
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                        Feature / Role
                                    </th>

                                    {{-- Role Columns --}}
                                    @foreach ($roles as $role)
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($permissions as $permission)
                                    <tr>
                                        {{-- Feature Name Row --}}
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r bg-gray-50">
                                            {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                        </td>

                                        {{-- Checkboxes for each Role --}}
                                        @foreach ($roles as $role)
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @php
                                                    // LOGIC: If it's Admin, it's ALWAYS checked and disabled.
                                                    // Otherwise, check if the role actually has the permission.
                                                    $isAdmin = $role->name === 'admin';
                                                    $hasPermission = $role->hasPermissionTo($permission->name);
                                                    $isChecked = $isAdmin || $hasPermission;
                                                    $isDisabled = $isAdmin; // Lock admin checkboxes
                                                @endphp

                                                <input type="checkbox" name="matrix[{{ $role->id }}][]"
                                                    value="{{ $permission->id }}"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                    @if ($isChecked) checked @endif
                                                    @if ($isDisabled) disabled @endif>

                                                {{-- CRITICAL: Disabled checkboxes don't submit data. 
         We must add a hidden input for Admin so the backend still knows they have the permission. --}}
                                                @if ($isAdmin)
                                                    <input type="hidden" name="matrix[{{ $role->id }}][]"
                                                        value="{{ $permission->id }}">
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700">
                            Save Changes
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
