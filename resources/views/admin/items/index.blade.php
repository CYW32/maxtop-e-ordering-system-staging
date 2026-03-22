<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase">
            {{ __('Item Master List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- SUCCESS NOTIFICATION ALERT --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show"
                    class="bg-green-50 border border-green-200 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm mb-6 transition-all">
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
                    <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- ERROR NOTIFICATION ALERT --}}
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show"
                    class="bg-red-50 border border-red-200 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm mb-6 transition-all">
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
                    <button @click="show = false" class="text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                @can('create_items')
                    <a href="{{ route('items.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-lg shadow-blue-100">
                        {{ __('+ New Item') }}
                    </a>
                @endcan
            </div>

            {{-- SEARCH TOOLBAR --}}
            <div class="bg-white p-4 md:p-6 rounded-[2rem] border border-gray-100 shadow-sm mb-6"
                x-data="{ showFilters: {{ request()->hasAny(['category', 'status']) ? 'true' : 'false' }} }">
                <form method="GET" action="{{ route('items.index') }}" class="flex flex-col gap-4">

                    {{-- Top Row: Text Search & Main Actions --}}
                    <div class="flex flex-col md:flex-row items-center gap-3 w-full">

                        {{-- Search Input --}}
                        <div class="relative flex-1 w-full">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Search by SKU or Display Name...') }}"
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
                            @if (request()->hasAny(['search', 'category', 'status']))
                                <a href="{{ route('items.index') }}"
                                    class="flex items-center justify-center bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 px-4 py-3 rounded-2xl transition-all shadow-sm border border-gray-200 hover:border-red-200"
                                    title="Clear All Filters">
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

                        {{-- Changed to Grid layout to prevent elements from crashing --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">

                            {{-- Category Filter --}}
                            <div class="w-full">
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Product Category') }}</label>
                                <select name="category"
                                    class="w-full py-3 px-4 rounded-2xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm text-gray-600 shadow-sm transition-shadow bg-white cursor-pointer"
                                    @change="$el.form.submit()">
                                    <option value="">{{ __('All Categories') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status Filter --}}
                            <div class="w-full">
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Listing Status') }}</label>
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

            {{-- MASTER LIST TABLE --}}
            <div class="bg-white shadow-sm sm:rounded-[2.5rem] border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('SKU / Identity') }}</th>
                            <th
                                class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Linked Catalogs') }}</th>
                            {{-- ARCHITECTURE FIX: Swapped Price for Status [Turn Context] --}}
                            <th
                                class="px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Listing Status') }}</th>
                            <th
                                class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Management') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @foreach ($items as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        @if ($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}"
                                                class="w-10 h-10 rounded-xl object-cover border border-gray-100">
                                        @else
                                            <div
                                                class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-[8px] font-black text-gray-300 uppercase">
                                                {{ __('No img') }}</div>
                                        @endif
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-black text-gray-800 uppercase leading-tight">{{ $item->name }}</span>
                                            <span
                                                class="text-[10px] font-mono text-blue-500 font-bold tracking-tighter uppercase">{{ $item->sku }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($item->catalogs as $catalog)
                                            <span
                                                class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-md text-[8px] font-black uppercase border border-indigo-100">
                                                {{ $catalog->name }}
                                            </span>
                                        @empty
                                            <span
                                                class="text-[9px] text-gray-300 italic uppercase">{{ __('Not Whitelisted') }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                {{-- STATUS BADGE: Fulfills UI Requirement [Turn Context] --}}
                                <td class="px-6 py-5 text-center">
                                    @php
                                        $hasBaseUnit = $item->activeUoms->contains('rate_qty', 1);
                                        $displayStatus =
                                            $item->status === 'active' && $hasBaseUnit ? 'Active' : 'inactive';
                                    @endphp

                                    <span
                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase border shadow-sm
                                        {{ $displayStatus === 'Active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                        {{ $displayStatus }}
                                    </span>

                                    @if ($item->status === 'active' && !$hasBaseUnit)
                                        <p class="text-[8px] text-red-400 font-black uppercase italic mt-1"
                                            title="{{ __('System locked: Missing Rate Qty 1') }}">
                                            {{ __('Invalid Config') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2">

                                        {{-- VIEW BUTTON (Eye Icon) --}}
                                        @can('view_items')
                                            <a href="{{ route('items.show', $item) }}"
                                                class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-green-600 hover:border-green-100 transition shadow-sm"
                                                title="{{ __('View Item') }}">
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
                                            <a href="{{ route('items.edit', $item) }}"
                                                class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-blue-600 hover:border-blue-100 transition shadow-sm"
                                                title="{{ __('Edit Item') }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                        @endcan

                                        {{-- DELETE BUTTON (Trash Icon) --}}
                                        @can('edit_items')
                                            <form action="{{ route('items.destroy', $item) }}" method="POST"
                                                class="inline-block m-0 p-0"
                                                onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this product? This action cannot be undone.') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-red-600 hover:border-red-100 hover:bg-red-50 transition shadow-sm"
                                                    title="{{ __('Delete Item') }}">
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
