<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase">
            {{ __('Item Master List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            @can('create_items')
                <a href="{{ route('items.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-lg shadow-blue-100">
                    {{ __('+ New Item') }}
                </a>
            @endcan
        </div>
            {{-- SEARCH TOOLBAR --}}
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('items.index') }}" class="flex gap-4">
                    <div class="flex-1 relative">
                        <x-text-input name="search" value="{{ request('search') }}" placeholder="{{ __('Search by SKU or Display Name...') }}" class="w-full pl-10 pr-4 py-3 rounded-2xl border-gray-100 focus:ring-blue-500" />
                        <svg class="w-5 h-5 text-gray-300 absolute left-3 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <button type="submit" class="bg-gray-900 hover:bg-black text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-md">
                        {{ __('Filter') }}
                    </button>
                </form>
            </div>

            {{-- MASTER LIST TABLE --}}
            <div class="bg-white shadow-sm sm:rounded-[2.5rem] border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('SKU / Identity') }}</th>
                            <th class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Linked Catalogs') }}</th>
                            {{-- ARCHITECTURE FIX: Swapped Price for Status [Turn Context] --}}
                            <th class="px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Listing Status') }}</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Management') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @foreach ($items as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        @if($item->image_path)
                                            <img src="{{ asset('storage/'.$item->image_path) }}" class="w-10 h-10 rounded-xl object-cover border border-gray-100">
                                        @else
                                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-[8px] font-black text-gray-300 uppercase">{{ __('No img') }}</div>
                                        @endif
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-800 uppercase leading-tight">{{ $item->name }}</span>
                                            <span class="text-[10px] font-mono text-blue-500 font-bold tracking-tighter uppercase">{{ $item->sku }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($item->catalogs as $catalog)
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-md text-[8px] font-black uppercase border border-indigo-100">
                                                {{ $catalog->name }}
                                            </span>
                                        @empty
                                            <span class="text-[9px] text-gray-300 italic uppercase">{{ __('Not Whitelisted') }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                {{-- STATUS BADGE: Fulfills UI Requirement [Turn Context] --}}
                                <td class="px-6 py-5 text-center">
                                    @php
                                        $hasBaseUnit = $item->activeUoms->contains('rate_qty', 1);
                                        $displayStatus = ($item->status === 'active' && $hasBaseUnit) ? 'Active' : 'inactive';
                                    @endphp
                                    
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border shadow-sm
                                        {{ $displayStatus === 'Active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                        {{ $displayStatus }}
                                    </span>

                                    @if($item->status === 'active' && !$hasBaseUnit)
                                        <p class="text-[8px] text-red-400 font-black uppercase italic mt-1" title="{{ __('System locked: Missing Rate Qty 1') }}">
                                            {{ __('Invalid Config') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        @can('edit_items')
                                            <a href="{{ route('items.edit', $item) }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-blue-600 hover:border-blue-100 transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </a>
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
