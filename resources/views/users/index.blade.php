<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Login Credentials') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- ALERTS: Success and Error Messages --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show"
                    class="bg-green-50 border border-green-200 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm transition-all mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span
                            class="text-xs font-black uppercase text-green-800 tracking-wide">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="p-1 text-green-400 hover:text-green-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show"
                    class="bg-red-50 border border-red-200 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm transition-all mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span
                            class="text-xs font-black uppercase text-red-800 tracking-wide">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="p-1 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- 1. CREATE BUTTON (TOP) --}}
            @can('create_users')
                <div class="mb-6">
                    <a href="{{ route('users.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-lg shadow-blue-100 inline-block mt-2">
                        {{ __('+ Create New Login') }}
                    </a>
                </div>
            @endcan

            {{-- Dynamic Scope Toggles (Only visible to users authorized to see both scopes) --}}
            @if ($canSwitchScope)
                <div class="flex items-center gap-2 border-b border-gray-100 pb-2 mb-4">
                    <a href="{{ route('users.index', array_merge(request()->query(), ['scope' => 'all'])) }}"
                        class="px-5 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all {{ $currentScope === 'all' ? 'bg-gray-900 text-white shadow-md' : 'bg-white text-gray-500 hover:text-gray-900 border border-gray-100 shadow-sm' }}">
                        {{ __('All System Logins') }}
                    </a>
                    <a href="{{ route('users.index', array_merge(request()->query(), ['scope' => 'assigned'])) }}"
                        class="px-5 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all {{ $currentScope === 'assigned' ? 'bg-gray-900 text-white shadow-md' : 'bg-white text-gray-500 hover:text-gray-900 border border-gray-100 shadow-sm' }}">
                        {{ __('My Assigned Customers') }}
                    </a>
                </div>
            @endif

            {{-- 2. SEARCH TOOLBAR (MIDDLE) --}}
            <div class="bg-white p-4 md:p-6 rounded-[2rem] border border-gray-100 shadow-sm mb-6"
                x-data="{ showFilters: {{ request()->hasAny(['role', 'status']) ? 'true' : 'false' }} }">
                <form method="GET" action="{{ route('users.index') }}" class="flex flex-col gap-4">

                    {{-- Preserve Scope State --}}
                    <input type="hidden" name="scope" value="{{ request('scope', $currentScope) }}">

                    {{-- Top Row: Text Search & Main Actions --}}
                    <div class="flex flex-col md:flex-row items-center gap-3 w-full">
                        <div class="relative flex-1 w-full">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Search Name, Login ID or Email...') }}"
                                class="w-full pl-12 pr-4 py-3 rounded-2xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-shadow shadow-sm" />
                            <svg class="w-5 h-5 text-gray-400 absolute left-5 top-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                            <button type="submit"
                                class="w-full md:w-auto bg-gray-900 hover:bg-black text-white px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all shadow-md whitespace-nowrap">
                                {{ __('Search') }}
                            </button>

                            <button type="button" @click="showFilters = !showFilters"
                                class="w-full md:w-auto flex items-center justify-center bg-white border border-gray-200 text-gray-600 hover:text-gray-900 px-5 py-3 rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all shadow-sm hover:bg-gray-50 whitespace-nowrap">
                                <span
                                    x-text="showFilters ? '{{ __('Hide Filters') }}' : '{{ __('More Filters') }}'"></span>
                                <svg class="w-4 h-4 ml-2 transition-transform duration-200"
                                    :class="showFilters ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            @if (request()->hasAny(['search', 'role', 'status']))
                                <a href="{{ route('users.index', ['scope' => $currentScope]) }}"
                                    class="flex items-center justify-center bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 px-4 py-3 rounded-2xl transition-all shadow-sm border border-gray-200 hover:border-red-200"
                                    title="{{ __('Clear All Filters') }}">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Bottom Row: Advanced Filters --}}
                    <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
                        class="pt-5 mt-2 border-t border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">

                            {{-- Role Filter --}}
                            <div class="w-full">
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Role') }}</label>
                                <select name="role"
                                    class="w-full py-3 px-4 rounded-2xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm font-bold text-gray-600 shadow-sm transition-shadow bg-white cursor-pointer"
                                    @change="$el.form.submit()">
                                    <option value="">{{ __('All Roles') }}</option>
                                    @foreach ($roles as $roleName)
                                        <option value="{{ $roleName }}"
                                            {{ request('role') == $roleName ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $roleName)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status Filter --}}
                            <div class="w-full">
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Status') }}</label>
                                <select name="status"
                                    class="w-full py-3 px-4 rounded-2xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm font-bold text-gray-600 shadow-sm transition-shadow bg-white cursor-pointer"
                                    @change="$el.form.submit()">
                                    <option value="">{{ __('All Status') }}</option>
                                    @foreach ($status as $statusName)
                                        <option value="{{ $statusName }}"
                                            {{ request('status') == $statusName ? 'selected' : '' }}>
                                            {{ ucfirst($statusName) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            {{-- Users Master Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('User / Login ID') }}</th>
                                <th
                                    class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Assigned Company') }}</th>
                                <th
                                    class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Role') }}</th>
                                <th
                                    class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Status') }}</th>
                                <th
                                    class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900">{{ $user->name }}</span>
                                            <span
                                                class="text-[10px] font-black text-blue-500 uppercase tracking-tighter mt-0.5">{{ $user->login_id }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $user->email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->company)
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-xs font-bold text-gray-700">{{ $user->company->company_name }}</span>
                                                <span class="text-[10px] font-mono text-gray-400 uppercase">
                                                    {{ $user->company->company_code ?? $user->company->branch_code }}
                                                </span>
                                            </div>
                                        @else
                                            <span
                                                class="px-2 py-1 rounded-md bg-gray-100 text-gray-400 text-[9px] font-black uppercase italic">
                                                {{ __('Internal / Unassigned') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @foreach ($user->roles as $role)
                                            <span
                                                class="px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 text-[9px] font-black uppercase border border-indigo-100">
                                                {{ str_replace('_', ' ', $role->name) }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tight {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            <span
                                                class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $user->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('users.edit', $user) }}"
                                                class="inline-flex items-center bg-white border border-gray-200 text-gray-700 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase hover:bg-gray-50 transition shadow-sm">
                                                {{ __('Edit Profile') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">
                                                {{ __('No user credentials found matching your filters.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="p-6 bg-gray-50 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
