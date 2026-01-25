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
                                {{-- Main HQ/Top-Level Row --}}
                                <tr class="border-b hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $user->name }}
                                                {{-- Fulfills Request: "MAIN HQ" tag only for Customers with no parent_id --}}
                                                @if ($user->hasRole('customer') && is_null($user->parent_id))
                                                    <span
                                                        class="ml-2 px-1.5 py-0.5 bg-blue-600 text-white rounded text-[10px] uppercase font-black tracking-tighter">
                                                        {{ __('MAIN HQ') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $user->login_id }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold uppercase text-gray-600">
                                        {{ $user->roles->pluck('name')->map(fn($n) => str_replace('_', ' ', $n))->implode(', ') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 rounded-full text-[10px] font-black uppercase {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('users.edit', $user) }}"
                                            class="inline-block bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-black uppercase hover:bg-gray-100 transition">
                                            {{ __('Edit') }}
                                        </a>
                                        {{-- Delete form removed from here to clean up UI --}}
                                    </td>
                                </tr>

                                {{-- Branch Sub-Rows: Fulfills "Sub line below HQ" requirement --}}
                                @if ($user->branches->isNotEmpty())
                                    @foreach ($user->branches as $branch)
                                        <tr class="border-b bg-gray-50/50 hover:bg-gray-100 transition-colors">
                                            <td class="px-6 py-3 pl-12"> {{-- Indented for visual sub-line --}}
                                                <div class="flex items-center">
                                                    <span class="text-gray-400 mr-2 text-lg">↳</span>
                                                    <div class="flex flex-col">
                                                        <div class="text-sm font-semibold text-gray-700">
                                                            {{ $branch->name }}</div>
                                                        <div class="text-[10px] text-gray-400 font-mono">
                                                            {{ $branch->login_id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-3 text-[10px] font-bold uppercase text-gray-400 italic">
                                                {{ __('Branch Account') }}
                                            </td>
                                            <td class="px-6 py-3">
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $branch->status === 'active' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                                    {{ $branch->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                <a href="{{ route('users.edit', $branch) }}"
                                                    class="inline-block bg-gray-200 text-gray-600 px-3 py-1 rounded-md text-[10px] font-bold uppercase hover:bg-gray-300 transition">
                                                    {{ __('Edit') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
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
