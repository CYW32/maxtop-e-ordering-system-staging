<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('Feature Access Control') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100">
                <div class="p-8 text-gray-900">
                    <div class="mb-8">
                        <h3 class="text-lg font-black uppercase text-gray-800 tracking-tight">
                            {{ __('Permission Matrix') }}</h3>
                        <p class="text-xs text-gray-500 uppercase font-bold mt-1">
                            {{ __('Check the box to enable a feature for a specific role.') }}</p>
                    </div>

                    {{-- ARCHITECTURE FIX: Updated route name to 'admin.roles.update' to match web.php definitions --}}
                    <form action="{{ route('admin.roles.update') }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto border border-gray-100 rounded-3xl">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                            {{ __('Feature / Role') }}
                                        </th>
                                        @foreach ($roles as $role)
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-50">
                                    @foreach ($permissions as $permission)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r bg-gray-50">
                                                {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                            </td>
                                            @foreach ($roles as $role)
                                                @php
                                                    $isAdmin = $role->name === 'admin';
                                                    $hasPermission = $role->hasPermissionTo($permission->name);
                                                    $isChecked = $isAdmin || $hasPermission;
                                                    $isDisabled = $isAdmin;
                                                @endphp
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <input type="checkbox" name="matrix[{{ $role->id }}][]"
                                                        value="{{ $permission->id }}"
                                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                        @if ($isChecked) checked @endif
                                                        @if ($isDisabled) disabled @endif>

                                                    {{-- SECURITY GUARD: Ensure Admin always retains all permissions [Backbone 2.a] --}}
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

                        <div class="mt-8 flex justify-end">
                            <x-primary-button
                                class="bg-blue-600 hover:bg-blue-700 px-8 py-3 rounded-xl text-[10px] font-black uppercase shadow-lg shadow-blue-100">
                                {{ __('Save Feature Matrix') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
