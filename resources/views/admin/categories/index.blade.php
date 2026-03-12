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
                <div class="bg-green-50 border border-green-200 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span
                        class="text-xs font-black uppercase text-green-800 tracking-wide">{{ session('success') }}</span>
                </div>
            @endif

            {{-- ERROR NOTIFICATION ALERT --}}
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-black uppercase text-red-800 tracking-wide">{{ session('error') }}</span>
                </div>
            @endif

            @can('create_items')
                <a href="{{ route('categories.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-lg shadow-blue-100 inline-block mt-2">
                    {{ __('+ New Category') }}
                </a>
            @endcan

            {{-- SEARCH TOOLBAR --}}
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('categories.index') }}" class="flex gap-4">
                    <div class="flex-1 relative">
                        <x-text-input name="search" value="{{ request('search') }}"
                            placeholder="{{ __('Search category names...') }}"
                            class="w-full pl-10 pr-4 py-3 rounded-2xl border-gray-100 focus:ring-blue-500" />
                        <svg class="w-5 h-5 text-gray-300 absolute left-3 top-3.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit"
                        class="bg-gray-900 hover:bg-black text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-md">
                        {{ __('Filter') }}
                    </button>
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
                                {{ __('Action') }}</th>
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
                                    @can('edit_items')
                                        <a href="{{ route('categories.edit', $category) }}"
                                            class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-blue-600 transition shadow-sm inline-block">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2.5">
                                                <path
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    @endcan
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
