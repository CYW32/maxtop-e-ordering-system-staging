<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">
                    {{ __('Product Catalog') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Browse available items and add them to your draft order.') }}</p>
            </div>

            {{-- Optional: Add a quick link to the cart in the header --}}
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

            {{-- Premium E-Commerce Search Bar with Integrated Category Dropdown --}}
            <div class="max-w-4xl mx-auto">
                <form method="GET" action="{{ route('customer.products.index') }}">

                    {{-- AMENDED: Responsive Flex Container --}}
                    {{-- flex-col-reverse puts Search on TOP and Category on BOTTOM for mobile --}}
                    <div
                        class="flex flex-col-reverse md:flex-row items-center w-full bg-white rounded-[1.5rem] md:rounded-full shadow-sm border border-gray-200 focus-within:shadow-md focus-within:border-brand-400 focus-within:ring-4 focus-within:ring-brand-500/10 transition-all overflow-hidden group">

                        {{-- Category Dropdown (Bottom on mobile, Left on desktop) --}}
                        <select name="category"
                            class="w-full md:w-auto h-12 md:h-14 pl-6 pr-8 py-0 border-none bg-gray-50 text-gray-700 text-sm font-semibold focus:ring-0 cursor-pointer border-t md:border-t-0 md:border-r border-gray-200 hover:bg-gray-100 transition-colors outline-none">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach ($availableCategories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Search Input & Button (Top on mobile, Right on desktop) --}}
                        <div class="flex items-center w-full md:flex-1 h-12 md:h-14">

                            {{-- Search Icon --}}
                            <div class="pl-4 pr-2 text-gray-400 group-focus-within:text-brand-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            {{-- Search Input --}}
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Search product name or SKU...') }}"
                                class="w-full h-full border-none ring-0 focus:ring-0 text-gray-700 placeholder-gray-400 text-sm md:text-base bg-transparent outline-none">

                            {{-- Submit Button --}}
                            <button type="submit"
                                class="h-full px-6 md:px-8 bg-brand-600 hover:bg-brand-700 text-white font-bold tracking-wide transition-colors flex items-center justify-center">
                                {{ __('Search') }}
                            </button>
                        </div>

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
                            {{ __('We couldn\'t find any products in your assigned catalog matching your current search.') }}
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
                        @php
                            // Safely grab the images array
                            $images = is_array($item->image_path)
                                ? $item->image_path
                                : ($item->image_path
                                    ? [$item->image_path]
                                    : []);
                        @endphp

                        <div x-data="{
                            activeImage: 0,
                            images: {{ json_encode($images) }},
                            nextImage() { this.activeImage = (this.activeImage + 1) % this.images.length; },
                            prevImage() { this.activeImage = (this.activeImage - 1 + this.images.length) % this.images.length; }
                        }"
                            class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-brand-500/10 hover:border-brand-200 transition-all duration-300 flex flex-col overflow-hidden group relative">


                            {{-- Edge-to-Edge Image Carousel --}}
                            {{-- AMENDED: Responsive Aspect Ratio Container --}}
                            {{-- Changed h-72 to h-56 (mobile) and md:h-72 (desktop) --}}
                            <div
                                class="relative w-full h-56 md:h-72 bg-gray-50 overflow-hidden border-b border-gray-100 group/carousel">

                                {{-- SKU Badge: Smaller on mobile --}}
                                <div class="absolute top-3 left-3 md:top-5 md:left-5 z-30">
                                    <span
                                        class="px-2 py-1 md:px-3 md:py-1.5 bg-white/95 backdrop-blur-md shadow-sm rounded-lg text-[10px] md:text-xs font-bold text-brand-700 tracking-wider border border-white">
                                        {{ $item->sku }}
                                    </span>
                                </div>

                                {{-- Has Images --}}
                                <template x-if="images.length > 0">
                                    <div class="w-full h-full relative">
                                        {{-- Image Loop --}}
                                        <template x-for="(img, index) in images" :key="index">
                                            {{-- AMENDED: Reduced padding on mobile (p-2) to maximize image size --}}
                                            <div x-show="activeImage === index" x-transition.opacity.duration.300ms
                                                class="absolute inset-0 w-full h-full p-2 md:p-4 flex items-center justify-center">

                                                <img :src="'/storage/' + img"
                                                    class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-700 ease-out mix-blend-multiply">
                                            </div>
                                        </template>

                                        {{-- Navigation Arrows: Smaller and always visible on mobile for better UX --}}
                                        <div x-show="images.length > 1"
                                            class="absolute inset-0 flex items-center justify-between px-2 md:px-3 z-20">
                                            <button type="button" @click.stop="prevImage()"
                                                class="p-1.5 md:p-2 bg-white/90 text-gray-800 rounded-full shadow-md backdrop-blur-sm transition-all md:opacity-0 md:group-hover/carousel:opacity-100">
                                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>
                                            <button type="button" @click.stop="nextImage()"
                                                class="p-1.5 md:p-2 bg-white/90 text-gray-800 rounded-full shadow-md backdrop-blur-sm transition-all md:opacity-0 md:group-hover/carousel:opacity-100">
                                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>

                                        {{-- Dots Indicator: Scaled down for mobile --}}
                                        <div x-show="images.length > 1"
                                            class="absolute bottom-3 md:bottom-4 left-0 right-0 flex justify-center gap-1 md:gap-1.5 z-20">
                                            <template x-for="(img, index) in images" :key="index">
                                                <button type="button" @click.stop="activeImage = index"
                                                    :class="activeImage === index ? 'bg-white w-4 md:w-5' :
                                                        'bg-white/50 w-1.5 md:w-2'"
                                                    class="h-1.5 md:h-2 rounded-full transition-all duration-300 shadow-sm"></button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                {{-- ... (No Image Fallback remains same) --}}
                            </div>

                            {{-- Product Details --}}
                            <div class="p-7 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-gray-900 leading-tight mb-2 line-clamp-1"
                                    title="{{ $item->name }}">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-sm text-gray-500 line-clamp-2 min-h-[2.5rem] leading-relaxed mb-6">
                                    {{ $item->description ?? __('No detailed description available.') }}
                                </p>

                                {{-- Ordering Block --}}
                                <div class="mt-auto pt-6 border-t border-gray-100">
                                    @if (auth()->user()->hasPendingOrder())
                                        <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl text-center">
                                            <div class="flex items-center justify-center text-amber-600 mb-1">
                                                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                    </path>
                                                </svg>
                                                <span class="text-sm font-bold">{{ __('Order Pending') }}</span>
                                            </div>
                                            <span
                                                class="block text-xs text-amber-700">{{ __('Recall pending order to add items.') }}</span>
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('reservation.store') }}"
                                            x-data="{
                                                selectedUom: 'individual',
                                                hasUoms: {{ $item->activeUoms->count() > 0 ? 'true' : 'false' }},
                                                draftMap: @js(
    $draftItems->where('item_id', $item->id)->mapWithKeys(
        fn($i) => [
            $i->uom_id ?? 'individual' => [
                'qty' => $i->quantity,
                'label' => $i->uom ? $i->uom->uom_name . ' (x' . $i->uom->rate_qty . ')' : __('Individual Unit'),
            ],
        ],
    ),
),
                                                qty: 1
                                            }" class="flex flex-col">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">

                                            {{-- Draft Indicator --}}
                                            <div class="space-y-2 mb-4" x-show="Object.keys(draftMap).length > 0"
                                                x-cloak>
                                                <template x-for="(data, uomKey) in draftMap" :key="uomKey">
                                                    <div
                                                        class="px-4 py-3 bg-brand-50 rounded-xl flex items-center justify-between border border-brand-100">
                                                        <span
                                                            class="text-xs font-semibold text-brand-700">{{ __('Already in Cart:') }}</span>
                                                        <span
                                                            class="text-xs font-black text-brand-800 bg-brand-100/60 px-2.5 py-1 rounded-md"
                                                            x-text="data.qty + ' ' + data.label"></span>
                                                    </div>
                                                </template>
                                            </div>

                                            {{-- UOM Selection --}}
                                            <div class="mb-3">
                                                <select name="uom_id" x-model="selectedUom"
                                                    :class="hasUoms ?
                                                        'border-gray-200 text-gray-900 bg-white hover:bg-gray-50' :
                                                        'bg-gray-100 text-gray-400 border-gray-200'"
                                                    class="w-full rounded-xl text-sm font-medium transition-colors focus:ring-brand-500 focus:border-brand-500 shadow-sm py-3 px-4 cursor-pointer">
                                                    @foreach ($item->activeUoms as $uom)
                                                        <option value="{{ $uom->id }}">{{ $uom->uom_name }}
                                                            (x{{ $uom->rate_qty }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Qty & Submit --}}
                                            <div class="flex gap-2 lg:gap-3 h-[48px] lg:h-[52px]">

                                                {{-- Premium Stacked Quantity Selector (^ and v) --}}
                                                <div
                                                    class="relative flex items-center border border-gray-200 rounded-xl bg-white shadow-sm w-[90px] lg:w-[105px] shrink-0 overflow-hidden">

                                                    {{-- Input Field --}}
                                                    <input name="quantity" type="number" min="1"
                                                        max="999" x-model="qty"
                                                        @change="qty = Math.max(1, parseInt(qty) || 1)"
                                                        class="flex-1 h-full w-full border-none pl-2 pr-8 text-center text-sm lg:text-base font-black text-gray-800 focus:ring-0 bg-white [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />

                                                    {{-- Stacked Up/Down Buttons Container --}}
                                                    <div
                                                        class="absolute right-0 top-0 bottom-0 w-8 flex flex-col border-l border-gray-200 bg-gray-50">

                                                        {{-- Up Button (^) --}}
                                                        <button type="button" @click="qty = qty + 1"
                                                            class="flex-1 flex justify-center items-center text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-colors border-b border-gray-200 cursor-pointer outline-none group">
                                                            <svg class="w-3.5 h-3.5 transform group-active:scale-90 transition-transform"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="3" d="M5 15l7-7 7 7"></path>
                                                            </svg>
                                                        </button>

                                                        {{-- Down Button (v) --}}
                                                        <button type="button" @click="qty = qty > 1 ? qty - 1 : 1"
                                                            class="flex-1 flex justify-center items-center text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-colors cursor-pointer outline-none group">
                                                            <svg class="w-3.5 h-3.5 transform group-active:scale-90 transition-transform"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </button>

                                                    </div>
                                                </div>

                                                {{-- Add to Draft Button --}}
                                                <button type="submit"
                                                    class="flex-1 inline-flex justify-center items-center h-full px-2 lg:px-4 bg-brand-600 border border-transparent rounded-xl font-bold text-[13px] lg:text-sm text-white tracking-wide hover:bg-brand-700 focus:bg-brand-700 active:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-all shadow-md shadow-brand-500/20">
                                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 mr-1.5 shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    <span class="whitespace-nowrap">{{ __('Add to Draft') }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
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
