<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-900 leading-tight">
                {{ __('Order Reservation (Cart)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- HEADER ROW: Action Buttons --}}
            <div class="flex items-center justify-between">
                {{-- 🔙 BACK TO PRODUCTS BUTTON --}}
                <a href="{{ route('customer.products.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white hover:bg-black rounded-xl shadow-md transition-all group font-bold text-sm">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Continue Shopping') }}
                </a>

                {{-- ✅ SUBMIT ORDER BUTTON --}}
                <form action="{{ route('reservation.submit') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-600 text-white hover:bg-brand-700 rounded-xl shadow-md shadow-brand-500/20 transition-all group font-bold text-sm">
                        {{ __('Submit Order Now') }}
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
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
                            {{ number_format($items->sum('quantity')) }}
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
                                        <span
                                            class="text-base font-bold text-gray-900">{{ number_format($orderItem->quantity) }}</span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        {{-- Delete/Remove Button --}}
                                        <form action="{{ route('reservation.destroy', $orderItem->id) }}"
                                            method="POST" class="m-0 inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('{{ __('Are you sure you want to remove this item?') }}')"
                                                class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
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
                                        <span class="text-base font-bold text-brand-600">
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

        </div>
    </div>
</x-app-layout>
