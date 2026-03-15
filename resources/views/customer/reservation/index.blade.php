<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('My Reservation Draft') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (!$draft || $items->isEmpty())
                <div class="bg-white p-16 rounded-[2.5rem] border border-gray-100 shadow-sm text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <p class="text-gray-400 font-black uppercase tracking-widest text-sm">
                            {{ __('Your draft is currently empty') }}</p>
                        <a href="{{ route('customer.products.index') }}"
                            class="mt-6 inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-black uppercase transition-all shadow-lg shadow-blue-100">
                            {{ __('Browse Product Catalog') }}
                        </a>
                    </div>
                </div>
            @else
                @php
                    // ARCHITECTURE: Group by item_id to cluster different packaging types under SKU [Backbone 4.a.2]
                    $groupedItems = $items->groupBy('item_id');
                    $totalQty = $items->sum('quantity');
                @endphp

                <div class="bg-white shadow-sm sm:rounded-[2.5rem] border border-gray-100 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Product Details') }}</th>
                                <th
                                    class="px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Selected Unit') }}</th>
                                <th
                                    class="px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Quantity') }}</th>
                                <th
                                    class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('Management') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @foreach ($groupedItems as $itemId => $entries)
                                @php $masterItem = $entries->first()->item; @endphp

                                {{-- SKU Group Header --}}
                                <tr class="bg-blue-50/30">
                                    <td colspan="4" class="px-8 py-3 border-y border-blue-100/50">
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="px-2 py-0.5 bg-blue-600 text-white text-[9px] font-mono font-black rounded uppercase tracking-tighter">
                                                {{ $masterItem->sku }}
                                            </span>
                                            <span
                                                class="text-xs font-black text-gray-900 uppercase tracking-tight">{{ $masterItem->name }}</span>
                                        </div>
                                    </td>
                                </tr>

                                @foreach ($entries as $orderItem)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-4"></td>
                                        <td class="px-6 py-4 text-center">
                                            {{-- STRICT UOM: Display packaging name and rate [Addendum 5.a] --}}
                                            <span
                                                class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight bg-blue-100 text-blue-700 border border-blue-200">
                                                {{ $orderItem->uom->uom_name }} (x{{ $orderItem->uom->rate_qty }})
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <form method="POST" action="{{ route('reservation.update', $orderItem) }}"
                                                class="flex items-center justify-center gap-3">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="quantity"
                                                    value="{{ $orderItem->quantity }}" min="1" max="999"
                                                    class="w-20 text-center border-gray-200 rounded-xl text-sm font-black focus:ring-blue-500">
                                                <button type="submit" title="{{ __('Update Quantity') }}"
                                                    class="text-blue-500 hover:text-blue-700 transition-transform active:scale-90">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="3">
                                                        <path d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-8 py-4 text-right">
                                            <form method="POST"
                                                action="{{ route('reservation.destroy', $orderItem) }}"
                                                onsubmit="return confirm('{{ __('Remove this item from your draft?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase tracking-widest transition-colors">
                                                    {{ __('Remove') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Price Blind Footer: Volume-based reservation [Backbone 4.b, 120] --}}
                    <div class="p-10 bg-gray-900 flex flex-col md:flex-row justify-between items-center gap-8">
                        <div class="flex flex-col text-center md:text-left">
                            <span
                                class="text-[10px] font-black uppercase text-gray-500 tracking-[0.2em] mb-1">{{ __('Total Reservation Volume') }}</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-white leading-none">{{ $totalQty }}</span>
                                <span
                                    class="text-sm font-bold text-gray-500 uppercase tracking-widest">{{ __('Units') }}</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('reservation.submit') }}"
                            onsubmit="return confirm('{{ __('Ready to submit for CS review?') }}');">
                            @csrf
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white py-5 px-14 rounded-2xl text-sm font-black uppercase shadow-xl shadow-green-900/40 transition-all hover:-translate-y-1">
                                {{ __('Submit for Review') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-2 text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest">{{ __('Prices are hidden for B2B confidentiality and validated during CS approval') }}</span>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
