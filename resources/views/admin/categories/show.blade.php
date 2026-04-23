<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('View Category') }}: <span class="text-blue-600">{{ $category->name }}</span>
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('categories.index') }}"
                    class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                    &larr; {{ __('Back To Product Categories') }}
                </a>
                @can('edit_items')
                    <a href="{{ route('categories.edit', $category) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-2xl text-[10px] font-black uppercase transition-all shadow-lg shadow-blue-100 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        {{ __('Edit') }}
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- TOP RIGHT ACTION BUTTONS --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('categories.index') }}"
                    class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                    &larr; {{ __('Back To Product Categories') }}
                </a>
                @can('edit_items')
                    <a href="{{ route('categories.edit', $category) }}"
                        class="bg-gray-900 hover:bg-black text-white py-3.5 px-8 rounded-[2rem] shadow-xl shadow-gray-200 transition-all uppercase text-[11px] font-black tracking-[0.1em] flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        {{ __('Edit') }}
                    </a>
                @endcan
            </div>

            {{-- Category Details Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                <div
                    class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Category Identity & Status') }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-1">{{ __('Category Name') }}</h3>
                        <p class="text-lg font-bold text-gray-900 uppercase">{{ $category->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-2">{{ __('Operational Status') }}
                        </h3>
                        <span
                            class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase border shadow-sm {{ $category->status === 'active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                            {{ $category->status === 'active' ? __('Active') : __('Inactive') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Assigned Items List --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                <div
                    class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        {{ __('Assigned Items') }} ({{ $category->items->count() }})
                    </div>
                </div>

                @if ($category->items->isEmpty())
                    <div
                        class="p-8 bg-gray-50 rounded-2xl border border-gray-100 text-gray-400 text-[11px] font-bold text-center uppercase tracking-wider">
                        {{ __('No items are currently assigned to this category.') }}
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($category->items as $item)
                            <div class="flex items-center p-4 border border-gray-100 rounded-xl bg-gray-50/50">
                                <div class="flex-1">
                                    <div class="text-[10px] font-black text-blue-500 uppercase tracking-tighter">
                                        {{ $item->sku }}</div>
                                    <div class="text-sm font-bold text-gray-800 leading-tight mt-0.5">
                                        {{ $item->name }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
