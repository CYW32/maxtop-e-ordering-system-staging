<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Header with Add Button --}}
                    <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">

                        {{-- THE TOOLBAR --}}
                        <x-filter-toolbar placeholder="Search name, ID...">

                            {{-- INJECTING THE EXTRA FILTER (The Slot) --}}
                            <select name="role"
                                class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Roles</option>
                                @foreach ($roles as $roleName)
                                    <option value="{{ $roleName }}"
                                        {{ request('role') == $roleName ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $roleName)) }}
                                    </option>
                                @endforeach
                            </select>

                        </x-filter-toolbar>

                        @can('create_users')
                            <a href="{{ route('users.create') }}"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center whitespace-nowrap h-[38px] flex items-center">
                                + Create User
                            </a>
                        @endcan
                    </div>

                    {{-- THE TABLE --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name / Login ID</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->login_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{-- Spatie helper to get role names --}}
                                        @foreach ($user->getRoleNames() as $role)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->status === 'active')
                                            <span class="text-green-600 font-bold text-xs uppercase">Active</span>
                                        @else
                                            <span class="text-red-600 font-bold text-xs uppercase">Deactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @can('edit_users')
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination Links --}}
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
