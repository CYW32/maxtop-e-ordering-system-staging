<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('📦 Product Item Master List') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">

                @can('create_items')
                    <a href="{{ route('items.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-black uppercase transition shadow-md">
                        + {{ __('Create New Item') }}
                    </a>
                @endcan

                {{-- Search Filter --}}
                <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                    <form action="{{ route('items.index') }}" method="GET" class="flex gap-4">
                        <x-text-input name="search" value="{{ request('search') }}" placeholder="Search SKU or Name..."
                            class="flex-1" />
                        <x-primary-button>{{ __('Filter') }}</x-primary-button>
                        @if (request('search'))
                            <a href="{{ route('items.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-800 text-white uppercase text-[10px] tracking-widest font-black">
                                <th class="px-6 py-4">{{ __('Image') }}</th>
                                <th class="px-6 py-4">{{ __('SKU / Name') }}</th>
                                <th class="px-6 py-4">{{ __('Price') }}</th>
                                <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($items as $item)
                                <tr
                                    class="transition-colors {{ $item->status === 'deactive' ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4">
                                        @if ($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}"
                                                class="h-12 w-12 object-cover rounded-lg border shadow-sm">
                                        @else
                                            <div
                                                class="h-12 w-12 bg-gray-100 flex items-center justify-center rounded-lg text-[10px] text-gray-400 font-bold uppercase border border-dashed border-gray-300">
                                                {{ __('No Image') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-black text-blue-600 uppercase">{{ $item->sku }}
                                        </div>
                                        <div class="text-sm font-bold text-gray-800">{{ $item->name }}</div>
                                        @if ($item->status === 'deactive')
                                            <span
                                                class="mt-1 inline-block px-2 py-0.5 bg-red-600 text-white text-[9px] font-black uppercase rounded shadow-sm">
                                                {{ __('Deactivated') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-mono text-sm font-bold text-gray-700">
                                        RM {{ number_format($item->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('items.edit', $item) }}"
                                            class="inline-block bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase hover:bg-gray-100 transition shadow-sm">
                                            {{ __('Edit Item') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t border-gray-50">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
