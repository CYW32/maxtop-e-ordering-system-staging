<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Product Categories') }}
            </h2>
            {{-- ARCHITECTURE FIX: Matches items index button style [User Request] --}}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- SUCCESS NOTIFICATION ALERT --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show"
                    class="bg-green-50 border border-green-200 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm transition-all">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span
                            class="text-xs font-black uppercase text-green-800 tracking-wide">{{ session('success') }}</span>
                    </div>
                    {{-- CLOSE BUTTON --}}
                    <button @click="show = false" class="p-1 text-green-400 hover:text-green-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- ERROR NOTIFICATION ALERT --}}
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show"
                    class="bg-red-50 border border-red-200 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm transition-all">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span
                            class="text-xs font-black uppercase text-red-800 tracking-wide">{{ session('error') }}</span>
                    </div>
                    {{-- CLOSE BUTTON --}}
                    <button @click="show = false" class="p-1 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @can('create_items')
                <a href="{{ route('categories.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-lg shadow-blue-100 inline-block mt-2">
                    {{ __('+ New Category') }}
                </a>
            @endcan

            {{-- SEARCH TOOLBAR --}}
            {{-- SEARCH TOOLBAR --}}
            <div class="bg-white p-4 md:p-6 rounded-[2rem] border border-gray-100 shadow-sm mb-6"
                x-data="{ showFilters: {{ request()->hasAny(['status']) ? 'true' : 'false' }} }">
                <form method="GET" action="{{ route('categories.index') }}" class="flex flex-col gap-4">

                    {{-- Top Row: Text Search & Main Actions --}}
                    <div class="flex flex-col md:flex-row items-center gap-3 w-full">

                        {{-- Search Input --}}
                        <div class="relative flex-1 w-full">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Search category names...') }}"
                                class="w-full pl-12 pr-4 py-3 rounded-2xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-shadow shadow-sm" />
                            <svg class="w-5 h-5 text-gray-400 absolute left-5 top-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2 w-full md:w-auto shrink-0">

                            {{-- Submit Search --}}
                            <button type="submit"
                                class="w-full md:w-auto bg-gray-900 hover:bg-black text-white px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all shadow-md whitespace-nowrap">
                                {{ __('Search') }}
                            </button>

                            {{-- Toggle Filters Button --}}
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

                            {{-- Clear All Button --}}
                            @if (request()->hasAny(['search', 'status']))
                                <a href="{{ route('categories.index') }}"
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

                    {{-- Bottom Row: Advanced Filters (Hidden by default) --}}
                    <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
                        class="pt-5 mt-2 border-t border-gray-100">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">

                            {{-- Status Filter --}}
                            <div class="w-full">
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Operational Status') }}</label>
                                <select name="status"
                                    class="w-full py-3 px-4 rounded-2xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm text-gray-600 shadow-sm transition-shadow bg-white cursor-pointer"
                                    @change="$el.form.submit()">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                        {{ __('Active') }}</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                        {{ __('Inactive') }}</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-[2.5rem] border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Category Name') }}</th>
                            <th
                                class="px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Operational Status') }}</th>
                            <th
                                class="px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Linked Items') }}</th>
                            <th
                                class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Management') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        {{-- ARCHITECTURE STANDARD: Maxtop Empty State Protocol --}}
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <span
                                        class="text-sm font-black text-gray-700 uppercase tracking-tight">{{ $category->name }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase border shadow-sm
                                        {{ $category->status === 'active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                        {{ $category->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center font-mono font-black text-xs text-gray-400">
                                    {{ $category->items_count }}
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2">

                                        {{-- VIEW BUTTON (Eye Icon) --}}
                                        @can('view_items')
                                            <a href="{{ route('categories.show', $category) }}"
                                                class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-green-600 hover:border-green-100 transition shadow-sm"
                                                title="{{ __('View Category') }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        @endcan

                                        {{-- EDIT BUTTON (Pencil Icon) --}}
                                        @can('edit_items')
                                            <a href="{{ route('categories.edit', $category) }}"
                                                class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-blue-600 hover:border-blue-100 transition shadow-sm"
                                                title="{{ __('Edit Category') }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                        @endcan

                                        {{-- DELETE BUTTON (Trash Icon) --}}
                                        @can('edit_items')
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                                class="inline-block m-0 p-0"
                                                onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this category? This action cannot be undone.') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-red-600 hover:border-red-100 hover:bg-red-50 transition shadow-sm"
                                                    title="{{ __('Delete Category') }}">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100" class="px-8 py-20 text-center bg-white">
                                    <div class="flex flex-col items-center justify-center gap-4">
                                        <div
                                            class="w-16 h-16 bg-gray-50 rounded-[2rem] flex items-center justify-center border border-gray-100 shadow-inner">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em]">{{ __('No Categories Found') }}</span>
                                            <p class="mt-1 text-[9px] font-bold text-gray-300 uppercase italic">
                                                {{ __('Try adjusting your search criteria or create a new category.') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $categories->links() }}</div>
        </div>
    </div>
</x-app-layout>
