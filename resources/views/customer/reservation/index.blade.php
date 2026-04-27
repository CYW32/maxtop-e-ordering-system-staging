<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-900 leading-tight">
                {{ __('Order Reservation (Cart)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen" x-data="{ globalTotal: {{ $items->sum('quantity') }} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- PREMIUM HEADER ROW: Action Buttons --}}
            <div
                class="flex items-center justify-between gap-4 bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100">

                {{-- 🔙 BACK TO PRODUCTS BUTTON (Always at Top) --}}
                <a href="{{ route('customer.products.index') }}"
                    class="w-full md:w-auto inline-flex justify-center items-center gap-2 px-6 py-3 bg-gray-50 text-gray-700 hover:bg-gray-100 hover:text-gray-900 border border-gray-200 rounded-xl transition-all font-bold text-sm shadow-sm outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Continue Shopping') }}
                </a>

                {{-- ✅ SUBMIT ORDER BUTTON (Desktop Only - Top Right) --}}
                <form action="{{ route('reservation.submit') }}" method="POST" class="hidden md:block m-0">
                    @csrf
                    <button type="submit"
                        class="inline-flex justify-center items-center gap-2 px-8 py-3 bg-brand-600 text-white hover:bg-brand-700 rounded-xl shadow-lg shadow-brand-500/30 transition-all font-black text-sm tracking-wide transform active:scale-95 outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        {{ __('Confirm & Submit Order') }}
                    </button>
                </form>
            </div>

            {{-- OVERVIEW SECTION --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">

                    {{-- Customer Info --}}
                    <div class="p-6">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Prepared For') }}</label>
                        <p class="text-lg font-bold text-gray-900">
                            {{ auth()->user()->name ?? __('Valued Client') }}
                        </p>
                    </div>

                    {{-- Current Date --}}
                    <div class="p-6">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Draft Date') }}</label>
                        <p class="text-base font-medium text-gray-900">
                            {{ now()->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    {{-- Unique Items Count --}}
                    <div class="p-6">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Total Unique Items') }}</label>
                        <p class="text-base font-medium text-gray-900">
                            {{ $items->count() }} {{ __('Items') }}
                        </p>
                    </div>

                    {{-- Total Quantity Summary --}}
                    <div class="p-6 bg-brand-50/30">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Total Quantity') }}</label>
                        <p class="text-lg font-bold text-brand-600">
                            {{-- x-text makes this number instantly update --}}
                            <span
                                x-text="globalTotal.toLocaleString()">{{ number_format($items->sum('quantity')) }}</span>
                            <span class="text-xs text-brand-400 font-normal ml-1">{{ __('Units') }}</span>
                        </p>
                    </div>

                </div>
            </div>

            {{-- TABLE SECTION --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-900 tracking-tight">{{ __('Items in Reservation') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-white text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="px-8 py-4 w-16 text-center">{{ __('No.') }}</th>
                                <th class="px-8 py-4">{{ __('Item Description') }}</th>
                                <th class="px-8 py-4 text-center">{{ __('Item UOM') }}</th>
                                <th class="px-8 py-4 text-center">{{ __('Quantity') }}</th>
                                <th class="px-8 py-4 w-24 text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($items as $index => $orderItem)
                                <tr class="group hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5 text-center font-medium text-gray-400 text-sm">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col gap-1">
                                            {{-- Item SKU --}}
                                            <span
                                                class="text-xs font-bold text-brand-600 uppercase">{{ $orderItem->item->sku ?? 'N/A' }}</span>
                                            {{-- Item Name --}}
                                            <span class="font-semibold text-gray-900 text-sm leading-tight">
                                                {{ $orderItem->item->name ?? __('Unknown Item') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 shadow-sm">
                                            {{ $orderItem->uom->uom_name ?? __('Unit') }}
                                            @if ($orderItem->uom && $orderItem->uom->rate_qty > 1)
                                                <span
                                                    class="ml-2 pl-2 border-l border-gray-200 text-gray-400 font-normal">x{{ $orderItem->uom->rate_qty }}</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <div class="flex items-center justify-center">

                                            {{-- Interactive Form (Auto-saves in BACKGROUND without reloading page) --}}
                                            <form action="{{ route('reservation.update', $orderItem->id) }}"
                                                method="POST" class="m-0" x-data="{
                                                    qty: {{ $orderItem->quantity }},
                                                    oldQty: {{ $orderItem->quantity }},
                                                    save() {
                                                        fetch($el.action, { method: 'POST', body: new FormData($el) });
                                                    }
                                                }">
                                                @csrf
                                                @method('PUT')

                                                <div
                                                    class="relative flex items-center border border-gray-200 rounded-lg bg-white shadow-sm w-20 h-10 shrink-0 overflow-hidden focus-within:border-brand-400 focus-within:ring-1 focus-within:ring-brand-500">

                                                    {{-- Input Field --}}
                                                    <input name="quantity" type="number" min="1" max="999"
                                                        x-model="qty"
                                                        @change="
                                                            let val = Math.max(1, parseInt(qty) || 1);
                                                            globalTotal = globalTotal - oldQty + val;
                                                            qty = val; oldQty = val;
                                                            save();
                                                        "
                                                        class="w-full h-full border-none pl-1 pr-7 text-center text-sm font-black text-gray-800 bg-white [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none focus:ring-0" />

                                                    {{-- Stacked Up/Down Buttons --}}
                                                    <div
                                                        class="absolute right-0 top-0 bottom-0 w-7 flex flex-col border-l border-gray-100 bg-gray-50">

                                                        {{-- Up Button (^) --}}
                                                        <button type="button"
                                                            @click="if(qty < 999) { qty++; globalTotal++; oldQty = qty; save(); }"
                                                            class="flex-1 flex justify-center items-center text-gray-400 hover:text-brand-600 hover:bg-brand-50 border-b border-gray-100 transition-colors group outline-none">
                                                            <svg class="w-3 h-3 transform group-active:scale-90 transition-transform"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="3" d="M5 15l7-7 7 7"></path>
                                                            </svg>
                                                        </button>

                                                        {{-- Down Button (v) --}}
                                                        <button type="button"
                                                            @click="if(qty > 1) { qty--; globalTotal--; oldQty = qty; save(); }"
                                                            class="flex-1 flex justify-center items-center text-gray-400 hover:text-brand-600 hover:bg-brand-50 transition-colors group outline-none">
                                                            <svg class="w-3 h-3 transform group-active:scale-90 transition-transform"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </button>

                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        {{-- Premium Delete/Remove Button --}}
                                        <form action="{{ route('reservation.destroy', $orderItem->id) }}"
                                            method="POST" class="m-0 inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('{{ __('Are you sure you want to remove this item from your cart?') }}')"
                                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-500 hover:text-white border border-red-100 hover:border-red-500 rounded-xl transition-all shadow-sm group outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1">
                                                <svg class="w-4 h-4 transition-transform group-hover:rotate-12"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                <span class="hidden lg:inline">{{ __('Remove') }}</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center">
                                        <div
                                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 border border-gray-100 mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-gray-900 mb-1">
                                            {{ __('Your reservation is empty') }}</h3>
                                        <p class="text-sm text-gray-500">
                                            {{ __('Browse the catalog to add items to your order.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        {{-- TOTAL SUM FOOTER --}}
                        @if ($items->isNotEmpty())
                            <tfoot class="bg-gray-50/50 border-t-2 border-gray-100">
                                <tr>
                                    <td colspan="3" class="px-8 py-6 text-right">
                                        <span
                                            class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Total Sum of Stock Order Quantity') }}</span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        {{-- The x-text="globalTotal" is what makes the magic live update happen here! --}}
                                        <span class="text-base font-bold text-brand-600" x-text="globalTotal">
                                            {{ number_format($items->sum('quantity')) }}
                                        </span>
                                    </td>
                                    <td></td> {{-- Empty cell for the Action column --}}
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- ✅ SUBMIT ORDER BUTTON (Mobile Only - Bottom of Page) --}}
            @if ($items->isNotEmpty())
                <div class="block md:hidden mt-2 mb-8">
                    <form action="{{ route('reservation.submit') }}" method="POST" class="m-0 w-full">
                        @csrf
                        <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 px-8 py-4 bg-brand-600 text-white hover:bg-brand-700 rounded-xl shadow-lg shadow-brand-500/30 transition-all font-black text-[15px] tracking-wide transform active:scale-95 outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Confirm & Submit Order') }}
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
