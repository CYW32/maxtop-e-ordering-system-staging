<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('My Assigned Customers') }}
            </h2>
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-2xl text-xs font-black uppercase transition-all shadow-lg shadow-blue-200">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Onboard New Login') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Search & Filter Toolbar: Fulfills UI consistency requirements --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('users.assigned') }}">
                    <x-filter-toolbar :placeholder="__('Search assigned names or login IDs...')" :showDates="false">
                        <select name="role"
                            class="text-xs border-gray-200 rounded-xl font-bold uppercase text-gray-600 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('All Roles') }}</option>
                            @foreach ($roles as $roleName)
                                <option value="{{ $roleName }}"
                                    {{ request('role') == $roleName ? 'selected' : '' }}>
                                    {{ ucfirst($roleName) }}
                                </option>
                            @endforeach
                        </select>
                    </x-filter-toolbar>
                </form>
            </div>

            {{-- Assigned Users Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Login Identity') }}</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Representing Business') }}</th>
                                <th
                                    class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Status') }}</th>
                                <th
                                    class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900">{{ $user->name }}</span>
                                            <span
                                                class="text-[10px] font-black text-blue-500 uppercase tracking-tighter">{{ $user->login_id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->company)
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="text-xs font-bold text-gray-700">{{ $user->company->company_name }}</span>
                                                    {{-- Visual Tag for HQ/Branch status based on Company hierarchy --}}
                                                    @if (is_null($user->company->parent_id))
                                                        <span
                                                            class="px-1.5 py-0.5 bg-blue-600 text-white rounded text-[8px] font-black uppercase">{{ __('HQ') }}</span>
                                                    @else
                                                        <span
                                                            class="px-1.5 py-0.5 bg-gray-100 text-gray-500 border border-gray-200 rounded text-[8px] font-black uppercase">{{ __('Branch') }}</span>
                                                    @endif
                                                </div>
                                                <span class="text-[10px] font-mono text-gray-400 uppercase">
                                                    {{ $user->company->company_code ?? $user->company->branch_code }}
                                                </span>
                                            </div>
                                        @else
                                            <span
                                                class="text-[10px] text-red-400 font-black uppercase italic">{{ __('Company Unassigned') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('users.edit', $user) }}"
                                            class="inline-flex items-center bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-gray-50 transition shadow-sm">
                                            {{ __('Edit Login') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">
                                            {{ __('No assigned customers found.') }}</p>
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
