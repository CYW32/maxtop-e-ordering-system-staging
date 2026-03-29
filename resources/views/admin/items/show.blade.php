<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('View Product Entity') }}: <span class="text-blue-600">{{ $item->sku }}</span>
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('items.index') }}"
                    class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                    &larr; {{ __('Back To Product Items') }}
                </a>
                @can('edit_items')
                    <a href="{{ route('items.edit', $item) }}"
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- TOP RIGHT ACTION BUTTONS --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('items.index') }}"
                    class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                    &larr; {{ __('Back To Product Items') }}
                </a>
                @can('edit_items')
                    <a href="{{ route('items.edit', $item) }}"
                        class="bg-gray-900 hover:bg-black text-white py-3.5 px-8 rounded-[2rem] shadow-xl shadow-gray-200 transition-all uppercase text-[11px] font-black tracking-[0.1em] flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        {{ __('Edit') }}
                    </a>
                @endcan
            </div>

            {{-- ARCHITECTURE GUARD: Transactional Integrity Warning --}}
            @if ($item->orderItems()->whereHas('order', fn($q) => $q->whereIn('status', ['approved', 'completed']))->exists())
                <div class="p-6 bg-amber-50 border border-amber-100 rounded-[2rem] flex gap-4 items-center">
                    <div
                        class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-amber-500 shadow-sm shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-[11px] font-black uppercase text-amber-900 tracking-tight">
                            {{ __('Core Identity Lock Active') }}
                        </h4>
                        <p class="text-[9px] font-bold text-amber-700 uppercase italic">
                            {{ __('Finalized transaction snapshots exist for this item. Identity modifications are restricted to maintain historical auditing integrity.') }}
                            [3.c.1, 4.b]
                        </p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- COLUMN 1: PRIMARY IDENTITY & WHITELISTING --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Info Card --}}
                    <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-[10px] font-black uppercase text-gray-400 mb-2">
                                    {{ __('Display Name') }}
                                </h4>
                                <div
                                    class="block w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-sm font-black text-gray-800 uppercase">
                                    {{ $item->name }}
                                </div>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black uppercase text-gray-400 mb-2">{{ __('System SKU') }}
                                </h4>
                                <div
                                    class="block w-full bg-blue-50/50 border border-blue-100 rounded-2xl p-4 text-sm font-mono font-black text-blue-600 uppercase tracking-tight">
                                    {{ $item->sku }}
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <h4 class="text-[10px] font-black uppercase text-gray-400 mb-2">
                                    {{ __('Specifications') }}</h4>
                                <div
                                    class="block w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-xs font-bold text-gray-600 uppercase min-h-[80px] whitespace-pre-wrap">
                                    {{ $item->description ?: __('No specifications provided.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- CATEGORIZATION LIST --}}
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                            <h3
                                class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-6 border-b border-gray-50 pb-2">
                                {{ __('Categorization') }} <span
                                    class="ml-2 text-blue-500">{{ $item->categories->count() }}</span>
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($item->categories as $category)
                                    <span
                                        class="px-4 py-2 bg-gray-50 text-gray-700 rounded-xl text-[10px] font-black uppercase border border-gray-200 shadow-sm">
                                        {{ $category->name }}
                                    </span>
                                @empty
                                    <p class="text-[9px] font-black text-gray-300 uppercase italic">
                                        {{ __('No Groups Defined') }}
                                    </p>
                                @endforelse
                            </div>
                        </div>

                        {{-- CATALOG WHITELIST LIST --}}
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                            <h3
                                class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-6 border-b border-gray-50 pb-2">
                                {{ __('Catalog Whitelist') }} <span
                                    class="ml-2 text-indigo-500">{{ $item->catalogs->count() }}</span>
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($item->catalogs as $catalog)
                                    <span
                                        class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase border border-indigo-100 shadow-sm">
                                        {{ $catalog->name }}
                                    </span>
                                @empty
                                    <p class="text-[9px] font-black text-gray-300 uppercase italic">
                                        {{ __('No Catalogs Defined') }}
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMN 2: STATUS & MEDIA --}}
                <div class="space-y-8">
                    {{-- Status Card --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                        <div class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest">
                            {{ __('Operational Status') }}
                        </div>

                        @php
                            $hasBaseUnit = $item->uoms->contains('rate_qty', 1);
                            $displayStatus =
                                $item->status === 'active' && $hasBaseUnit
                                    ? 'Active & Orderable'
                                    : 'Deactivated (Hold)';
                        @endphp

                        <div
                            class="w-full rounded-2xl p-4 text-xs font-black uppercase text-center border
                            {{ $displayStatus === 'Active & Orderable' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                            {{ __($displayStatus) }}
                        </div>

                        @if ($item->status === 'active' && !$hasBaseUnit)
                            <p class="text-[9px] text-red-500 mt-4 uppercase font-black text-center italic">
                                {{ __('System Locked: Missing Base Unit (Rate 1)') }}
                            </p>
                        @endif
                    </div>

                    {{-- Media Gallery (WITH LIGHTBOX) --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                        x-data='{ 
                            lightboxOpen: false, 
                            lightboxIndex: 0, 
                            images: {!! json_encode(
                                collect((array) $item->image_path)->filter()->map(fn($p) => asset('storage/' . $p))->values()->all(),
                            ) !!} 
                         }'>

                        <div
                            class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest flex justify-between">
                            <span>{{ __('Media Gallery') }}</span>
                            <span class="text-blue-500 text-[8px]">{{ __('Click to zoom') }}</span>
                        </div>

                        <div class="flex flex-col items-center justify-center w-full">
                            @php
                                $itemImages = is_array($item->image_path)
                                    ? array_filter($item->image_path)
                                    : array_filter([$item->image_path]);
                            @endphp

                            @if (!empty($itemImages))
                                <div
                                    class="grid gap-4 w-full {{ count($itemImages) === 1 ? 'grid-cols-1' : 'grid-cols-2' }}">
                                    @foreach ($itemImages as $index => $img)
                                        <div class="relative w-full aspect-square overflow-hidden rounded-[2rem] border border-gray-100 shadow-sm bg-gray-50 cursor-pointer group"
                                            @click="lightboxIndex = {{ $index }}; lightboxOpen = true">
                                            <img src="{{ asset('storage/' . $img) }}"
                                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            <div
                                                class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="w-full aspect-square bg-gray-50 border-2 border-dashed border-gray-200 rounded-[2rem] flex flex-col items-center justify-center text-gray-300">
                                    <svg class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span
                                        class="text-[9px] font-black uppercase tracking-widest">{{ __('No Image Uploaded') }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- 全屏画廊组件 --}}
                        <template x-teleport="body">
                            <div x-show="lightboxOpen"
                                class="fixed inset-0 z-[9999] bg-black/95 flex items-center justify-center backdrop-blur-sm"
                                @keydown.escape.window="lightboxOpen = false"
                                @keydown.right.window="lightboxIndex = (lightboxIndex + 1) % images.length"
                                @keydown.left.window="lightboxIndex = (lightboxIndex - 1 + images.length) % images.length"
                                style="display: none;">

                                <button @click="lightboxOpen = false"
                                    class="absolute top-6 right-6 text-white hover:bg-red-500 rounded-full p-2 z-[100] transition-colors">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <button @click="lightboxIndex = (lightboxIndex - 1 + images.length) % images.length"
                                    x-show="images.length > 1"
                                    class="absolute left-6 text-white/50 hover:text-white bg-white/10 rounded-full p-4 transition-all z-[100]">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>

                                <div class="relative w-full h-full flex items-center justify-center p-20">
                                    <img :src="images[lightboxIndex]"
                                        class="max-h-full max-w-full object-contain rounded-xl shadow-2xl transition-all">
                                    <div
                                        class="absolute bottom-10 bg-black/50 text-white text-[10px] font-black px-6 py-2 rounded-full tracking-widest">
                                        <span x-text="lightboxIndex + 1"></span> / <span
                                            x-text="images.length"></span>
                                    </div>
                                </div>

                                <button @click="lightboxIndex = (lightboxIndex + 1) % images.length"
                                    x-show="images.length > 1"
                                    class="absolute right-6 text-white/50 hover:text-white bg-white/10 rounded-full p-4 transition-all z-[100]">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Unit of Measure (UOM) Configurations container --}}
            <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">

                <div class="flex flex-col mb-10 border-b border-gray-50 pb-6">
                    <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        {{ __('Unit of Measure (UOM) Configurations') }}
                    </h3>
                    <p class="text-[8px] font-bold text-gray-300 uppercase italic mt-1">
                        {{ __('Pricing source for all orders.') }} [Addendum 5.a]
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead>
                            <tr class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">
                                <th class="px-4 py-4 text-left">{{ __('Unit Name') }}</th>
                                <th class="px-4 py-4 text-center">{{ __('Rate (Base 1)') }}</th>
                                <th class="px-4 py-4 text-right">{{ __('Internal Price (RM)') }}</th>
                                <th class="px-4 py-4 text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($item->uoms as $uom)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-6">
                                        <div
                                            class="text-[11px] font-black uppercase text-gray-800 flex items-center gap-2">
                                            {{ $uom->uom_name }}
                                            @if ($uom->rate_qty == 1)
                                                <span
                                                    class="px-2 py-0.5 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-md text-[8px] font-black uppercase">{{ __('Base') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-6 text-center">
                                        <span class="text-[12px] font-mono font-black text-blue-600">
                                            {{ number_format($uom->rate_qty, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-6 text-right">
                                        <span class="text-[12px] font-mono font-black text-gray-800">
                                            {{ number_format($uom->price, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-6 text-center">
                                        <span
                                            class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase border
                                            {{ $uom->status === 'active' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-gray-50 text-gray-500 border-gray-200' }}">
                                            {{ $uom->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center">
                                        <div
                                            class="inline-block p-6 border-2 border-dashed border-gray-100 rounded-[2rem] bg-gray-50/30">
                                            <p class="text-[9px] font-black text-gray-300 uppercase italic">
                                                {{ __('No packaging units defined. This item is suppressed from Catalogs.') }}
                                                [3.a.3]
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>
