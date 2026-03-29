<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">
                    {{ __('Product Catalog') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Browse available items and add them to your draft order.') }}
                </p>
            </div>

            <a href="{{ route('reservation.index') }}"
                class="inline-flex items-center px-4 py-2 bg-brand-50 text-brand-700 rounded-lg font-bold text-sm hover:bg-brand-100 transition-colors border border-brand-200 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                {{ __('View My Cart') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- Integrated Search & Filter Bar --}}
            <div class="max-w-4xl mx-auto">
                <form method="GET" action="{{ route('customer.products.index') }}">
                    <div
                        class="relative flex items-center w-full h-14 bg-white rounded-full shadow-sm border border-gray-200 focus-within:shadow-md focus-within:border-brand-400 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all overflow-hidden group">

                        {{-- Category Selection --}}
                        <select name="category"
                            class="h-full pl-6 pr-8 py-0 border-none bg-gray-50 text-gray-700 text-sm font-semibold focus:ring-0 cursor-pointer border-r border-gray-200 hover:bg-gray-100 transition-colors outline-none">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach ($availableCategories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Search Icon --}}
                        <div class="pl-4 pr-2 text-gray-400 group-focus-within:text-brand-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        {{-- Input Field --}}
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ __('Search product name or SKU...') }}"
                            class="w-full h-full border-none ring-0 focus:ring-0 text-gray-700 placeholder-gray-400 text-base bg-transparent outline-none">

                        <button type="submit"
                            class="h-full px-8 bg-brand-600 hover:bg-brand-700 text-white font-bold tracking-wide transition-colors flex items-center">
                            {{ __('Search') }}
                        </button>
                    </div>
                </form>
            </div>

            @if ($items->isEmpty())
                <div class="bg-white py-24 px-6 rounded-3xl border border-gray-100 text-center shadow-sm">
                    <div class="flex flex-col items-center max-w-sm mx-auto">
                        <div
                            class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 border border-gray-100">
                            <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('No products found') }}</h3>
                        <p class="text-base text-gray-500">
                            {{ __('We could not find any products matching your current search or category filter.') }}
                        </p>
                        @if (request()->anyFilled(['search', 'category']))
                            <a href="{{ route('customer.products.index') }}"
                                class="mt-6 inline-flex items-center text-sm font-bold text-brand-600 hover:text-brand-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                {{ __('Clear Filters') }}
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
                    @foreach ($items as $item)
                        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 flex flex-col overflow-hidden group"
                            x-data='{ 
                                lightboxOpen: false, 
                                lightboxIndex: 0, 
                                cardIndex: 0, 
                                images: {!! json_encode(
                                    collect((array) $item->image_path)->filter()->map(fn($p) => asset('storage/' . $p))->values()->all(),
                                ) !!} 
                             }'>

                            {{-- Product Media Container with In-Card Carousel --}}
                            <div
                                class="relative w-full h-72 bg-white overflow-hidden border-b border-gray-100 flex items-center justify-center">

                                @if (count((array) $item->image_path) > 0)
                                    {{-- Product Image (Contain mode for full visibility) --}}
                                    <img :src="images[cardIndex]"
                                        src="{{ asset('storage/' . ((array) $item->image_path)[0]) }}"
                                        @click="lightboxIndex = cardIndex; lightboxOpen = true"
                                        class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-700 cursor-zoom-in">

                                    {{-- Navigation Arrows (Visible on Hover) --}}
                                    <div x-show="images.length > 1" style="display: none;"
                                        class="absolute inset-0 flex items-center justify-between px-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-20">
                                        <button type="button"
                                            @click.stop.prevent="cardIndex = (cardIndex - 1 + images.length) % images.length"
                                            class="w-8 h-8 flex items-center justify-center bg-white/90 hover:bg-white text-gray-800 rounded-full shadow-lg pointer-events-auto transition-transform hover:scale-110 active:scale-95 backdrop-blur-sm border border-gray-100">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <button type="button"
                                            @click.stop.prevent="cardIndex = (cardIndex + 1) % images.length"
                                            class="w-8 h-8 flex items-center justify-center bg-white/90 hover:bg-white text-gray-800 rounded-full shadow-lg pointer-events-auto transition-transform hover:scale-110 active:scale-95 backdrop-blur-sm border border-gray-100">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Navigation Dots --}}
                                    <div x-show="images.length > 1" style="display: none;"
                                        class="absolute bottom-4 left-0 right-0 flex justify-center gap-1.5 z-20 pointer-events-none">
                                        <template x-for="(img, idx) in images" :key="idx">
                                            <div class="w-1.5 h-1.5 rounded-full transition-all duration-300 shadow-sm border border-gray-300/50"
                                                :class="cardIndex === idx ? 'bg-gray-800 scale-150' : 'bg-gray-300'">
                                            </div>
                                        </template>
                                    </div>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50/80">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span
                                            class="text-sm font-semibold text-gray-400 tracking-wide">{{ __('No Image Available') }}</span>
                                    </div>
                                @endif

                                {{-- SKU Badge --}}
                                <div class="absolute top-5 left-5 z-20">
                                    <span
                                        class="px-3 py-1.5 bg-white/95 backdrop-blur-md shadow-sm rounded-lg text-xs font-bold text-brand-700 border border-white uppercase tracking-wider">{{ $item->sku }}</span>
                                </div>
                            </div>

                            {{-- Product Details --}}
                            <div class="p-7 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-gray-900 line-clamp-1" title="{{ $item->name }}">
                                    {{ $item->name }}</h3>
                                <p class="text-sm text-gray-500 line-clamp-2 min-h-[2.5rem] mb-6 leading-relaxed">
                                    {{ $item->description ?? __('No detailed description available.') }}</p>

                                <div class="mt-auto pt-6 border-t border-gray-100">
                                    @if (auth()->user()->hasPendingOrder())
                                        <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl text-center">
                                            <span
                                                class="text-sm font-bold text-amber-600 uppercase tracking-tight">{{ __('Order Pending Approval') }}</span>
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('reservation.store') }}"
                                            class="flex flex-col">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">

                                            {{-- Selection of UOM --}}
                                            <div class="mb-3">
                                                <select name="uom_id"
                                                    class="w-full rounded-xl text-sm font-medium border-gray-200 py-3 px-4 shadow-sm focus:ring-brand-500 focus:border-brand-500">
                                                    @foreach ($item->activeUoms as $uom)
                                                        <option value="{{ $uom->id }}">{{ $uom->uom_name }}
                                                            (x{{ $uom->rate_qty }})</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Quantity Input & Action Button --}}
                                            <div class="flex gap-3">
                                                <input name="quantity" type="number" value="1" min="1"
                                                    class="w-24 rounded-xl border-gray-200 font-bold text-center shadow-sm focus:ring-brand-500 focus:border-brand-500">
                                                <button type="submit"
                                                    class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl py-3 shadow-md shadow-brand-500/20 transition-all uppercase tracking-wide">
                                                    {{ __('Add to Draft') }}
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Fullscreen Modal Lightbox --}}
                            <template x-teleport="body">
                                <div x-show="lightboxOpen"
                                    class="fixed inset-0 z-[9999] bg-black/95 flex items-center justify-center backdrop-blur-sm"
                                    @keydown.escape.window="lightboxOpen = false"
                                    @keydown.right.window="lightboxIndex = (lightboxIndex + 1) % images.length"
                                    @keydown.left.window="lightboxIndex = (lightboxIndex - 1 + images.length) % images.length"
                                    style="display: none;">

                                    {{-- Exit Button --}}
                                    <button @click="lightboxOpen = false"
                                        class="absolute top-6 right-6 text-white bg-white/10 hover:bg-red-500 rounded-full p-2 z-[100] transition-colors">
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    {{-- Previous Arrow --}}
                                    <button
                                        @click="lightboxIndex = (lightboxIndex - 1 + images.length) % images.length"
                                        x-show="images.length > 1"
                                        class="absolute left-4 md:left-6 text-white/50 hover:text-white bg-white/10 rounded-full p-4 z-[100] transition-all">
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>

                                    {{-- High-Res Image Display --}}
                                    <div class="relative w-full h-full flex items-center justify-center p-4 md:p-12">
                                        <img :src="images[lightboxIndex]"
                                            class="max-h-full max-w-full object-contain rounded-xl shadow-2xl transition-all duration-300">
                                        <div
                                            class="absolute bottom-10 bg-black/50 text-white text-[10px] font-black tracking-widest px-4 py-2 rounded-full uppercase">
                                            <span x-text="lightboxIndex + 1"></span> / <span
                                                x-text="images.length"></span> {{ __('Photos') }}
                                        </div>
                                    </div>

                                    {{-- Next Arrow --}}
                                    <button @click="lightboxIndex = (lightboxIndex + 1) % images.length"
                                        x-show="images.length > 1"
                                        class="absolute right-4 md:right-6 text-white/50 hover:text-white bg-white/10 rounded-full p-4 z-[100] transition-all">
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="mt-12">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
