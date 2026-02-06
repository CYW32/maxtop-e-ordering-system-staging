<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Order Details') }}: {{ $order->order_number }}
            </h2>
            <a href="{{ route('customer.orders.index') }}" class="text-xs font-bold text-blue-600 uppercase">←
                {{ __('Back to History') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        {{-- Fulfills Request: Recall functionality --}}
        @if ($order->status === 'pending')
            <form action="{{ route('reservation.recall', $order) }}" method="POST"
                onsubmit="return confirm('Move this order back to draft for editing?');">
                @csrf
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-xs font-black uppercase transition shadow-md">
                    {{ __('Recall to Draft') }}
                </button>
            </form>
        @endif
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                // Group by item_id to handle different UOMs for the same SKU [Addendum 5.a]
                $groupedItems = $order->items->groupBy('item_id');
                $totalQty = $order->items->sum('quantity');
            @endphp

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase">
                                {{ __('Item Description') }}</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase">
                                {{ __('Packaging Unit') }}</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase">
                                {{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($groupedItems as $itemId => $entries)
                            @php $masterItem = $entries->first()->item; @endphp
                            <tr class="bg-blue-50/20">
                                <td colspan="3" class="px-8 py-3">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-[10px] font-mono font-black bg-blue-600 text-white px-2 py-0.5 rounded">{{ $masterItem->sku }}</span>
                                        <span class="text-xs font-bold text-gray-900">{{ $masterItem->name }}</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($entries as $item)
                                <tr>
                                    <td></td>
                                    <td class="px-6 py-4 text-center">
                                        {{-- ARCHITECTURE: Distinguish between UOM and Individual [4] --}}
                                        <span
                                            class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $item->uom_id ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $item->uom?->uom_name ?? __('Individual Unit') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                                        {{ $item->quantity }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>

                {{-- Footer: Fulfills Price Blind Policy [4.b] --}}
                <div class="p-8 bg-gray-900 text-white flex justify-between items-center">
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray-500 tracking-widest">
                            {{ __('Total Order Volume') }}</p>
                        <p class="text-2xl font-black">{{ $totalQty }} <span
                                class="text-sm font-bold text-gray-500 uppercase">{{ __('Units') }}</span></p>
                    </div>
                    <div class="text-right">
                        <span
                            class="px-4 py-2 bg-blue-600/20 border border-blue-500/30 rounded-xl text-[10px] font-black uppercase text-blue-400">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
