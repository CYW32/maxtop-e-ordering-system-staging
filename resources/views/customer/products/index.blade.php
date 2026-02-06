<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Available Products') }}
            </h2>

            {{-- Category Filter Toolbar: Fulfills Whitelist Logic [8.a.3] --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('customer.products.index') }}"
                    class="px-4 py-2 rounded-full text-[10px] font-black uppercase transition-all border {{ !request('category') ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-100' : 'bg-white text-gray-500 border-gray-100 hover:bg-gray-50' }}">
                    {{ __('All Items') }}
                </a>
                @foreach ($availableCategories as $cat)
                    <a href="{{ route('customer.products.index', ['category' => $cat->id]) }}"
                        class="px-4 py-2 rounded-full text-[10px] font-black uppercase transition-all border {{ request('category') == $cat->id ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-100' : 'bg-white text-gray-500 border-gray-100 hover:bg-gray-50' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Search Toolbar --}}
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('customer.products.index') }}">
                    <x-filter-toolbar :placeholder="__('Search product name or SKU...')" :showDates="false">
                        @if (request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                    </x-filter-toolbar>
                </form>
            </div>

            @if ($items->isEmpty())
                <div class="bg-white p-20 rounded-[3rem] border border-gray-100 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="text-gray-400 font-black uppercase tracking-widest">
                            {{ __('No products found in your whitelist.') }}</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($items as $item)
                        <div
                            class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col hover:shadow-xl hover:shadow-blue-50 transition-all duration-300">
                            {{-- Product Image --}}
                            <div class="relative mb-4 group">
                                @if ($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}"
                                        class="w-full h-48 object-cover rounded-[1.5rem] shadow-sm">
                                @else
                                    <div
                                        class="w-full h-48 bg-gray-50 flex items-center justify-center rounded-[1.5rem] border border-dashed border-gray-200">
                                        <span
                                            class="text-[10px] font-black text-gray-300 uppercase italic">{{ __('No Image') }}</span>
                                    </div>
                                @endif
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="px-2 py-1 bg-white/90 backdrop-blur shadow-sm rounded-lg text-[9px] font-mono font-black text-blue-600 uppercase">
                                        {{ $item->sku }}
                                    </span>
                                </div>
                            </div>

                            {{-- Item Info --}}
                            <div class="flex-1">
                                <h3 class="text-sm font-black text-gray-900 leading-tight mb-1">{{ $item->name }}
                                </h3>
                                <p class="text-[10px] text-gray-400 line-clamp-2 mb-4 leading-relaxed italic">
                                    {{ $item->description ?? __('No detailed description available.') }}
                                </p>
                            </div>

                            {{-- Ordering Logic: Blocked if Pending Review [11.b] --}}
                            <div class="mt-4 pt-4 border-t border-gray-50">
                                @if (auth()->user()->hasPendingOrder())
                                    <div class="bg-amber-50 border border-amber-100 p-3 rounded-2xl text-center">
                                        <span
                                            class="block text-[10px] text-amber-700 font-black uppercase tracking-tighter">{{ __('Order Pending Review') }}</span>
                                        <span
                                            class="block text-[8px] text-amber-600 mt-1 italic">{{ __('Recall pending order to enable edits.') }}</span>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('reservation.store') }}"
                                        x-data="{
                                            selectedUom: 'individual',
                                            hasUoms: {{ $item->activeUoms->count() > 0 ? 'true' : 'false' }},
                                            {{-- ARCHITECTURE FIX: Map both UOM IDs and 'individual' to an object containing QTY and LABEL [3, 4] --}}
                                            draftMap: @js(
    $draftItems->where('item_id', $item->id)->mapWithKeys(
        fn($i) => [
            $i->uom_id ?? 'individual' => [
                'qty' => $i->quantity,
                // FIX: Concatenate rate_qty to label so it shows 'Box (x12)' instead of just 'Box'
                'label' => $i->uom ? $i->uom->uom_name . ' (x' . $i->uom->rate_qty . ')' : __('Individual'),
            ],
        ],
    ),
)
                                        }" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $item->id }}">

                                        <div>
                                            {{-- FIX: Iterate through ALL drafts in the map, regardless of dropdown selection --}}
                                            <div class="my-4 space-y-2">
                                                <template x-for="(data, uomKey) in draftMap" :key="uomKey">
                                                    <div
                                                        class="mt-2 px-3 py-2 bg-indigo-50 rounded-xl flex items-center justify-between border border-indigo-100 animate-pulse">
                                                        <span
                                                            class="text-[9px] font-black text-indigo-600 uppercase">{{ __('In Draft') }}</span>
                                                        <span class="text-xs font-black text-indigo-700"
                                                            x-text="data.qty + ' ' + data.label + ' ' + '{{ __('Units') }}'">
                                                        </span>
                                                    </div>
                                                </template>
                                            </div>

                                            <x-input-label :value="__('Select Packaging Unit')"
                                                class="text-[9px] font-black text-gray-400 uppercase mb-1" />

                                            <select name="uom_id" x-model="selectedUom" :disabled="!hasUoms"
                                                :class="hasUoms
                                                    ?
                                                    'bg-blue-50 border-blue-200 text-blue-700 focus:ring-blue-500' :
                                                    'bg-gray-50 border-gray-100 text-gray-400 cursor-not-allowed'"
                                                class="w-full rounded-xl text-xs font-bold transition-all">
                                                <option value="individual">{{ __('Individual Unit') }}</option>
                                                @foreach ($item->activeUoms as $uom)
                                                    <option value="{{ $uom->id }}">{{ $uom->uom_name }}
                                                        (x{{ $uom->rate_qty }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="flex gap-2">
                                            <x-text-input name="quantity" type="number" value="1" min="1"
                                                max="999"
                                                class="w-20 rounded-xl border-gray-100 text-sm font-black text-center" />

                                            <x-primary-button
                                                class="flex-1 justify-center bg-blue-600 hover:bg-blue-700 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all shadow-lg shadow-blue-100">
                                                {{ __('Add To Draft') }}
                                            </x-primary-button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
