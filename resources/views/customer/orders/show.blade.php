<x-app-layout>
    {{-- Keeping the header slot minimal just for the title/status if your layout ever decides to show it --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-900 leading-tight">
                {{ __('Order Details') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- HEADER ROW: Back Button & Status Badge --}}
            <div class="flex items-center justify-between">
                {{-- 🔙 HIGH-CONTRAST BACK BUTTON --}}
                <a href="{{ route('customer.orders.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white hover:bg-black rounded-xl shadow-md transition-all group font-bold text-sm">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to Order') }}
                </a>

                {{-- Professional Status Badge --}}
                <div>
                    @php
                        $statusConfig = [
                            'pending' => [
                                'bg' => 'bg-amber-50',
                                'text' => 'text-amber-700',
                                'border' => 'border-amber-100',
                                'dot' => 'bg-amber-500',
                            ],
                            'approved' => [
                                'bg' => 'bg-brand-50',
                                'text' => 'text-brand-700',
                                'border' => 'border-brand-100',
                                'dot' => 'bg-brand-500',
                            ],
                            'in_transit' => [
                                'bg' => 'bg-indigo-50',
                                'text' => 'text-indigo-700',
                                'border' => 'border-indigo-100',
                                'dot' => 'bg-indigo-500',
                            ],
                            'completed' => [
                                'bg' => 'bg-emerald-50',
                                'text' => 'text-emerald-700',
                                'border' => 'border-emerald-100',
                                'dot' => 'bg-emerald-500',
                            ],
                            'cancelled' => [
                                'bg' => 'bg-red-50',
                                'text' => 'text-red-700',
                                'border' => 'border-red-100',
                                'dot' => 'bg-red-500',
                            ],
                        ];
                        $cfg = $statusConfig[$order->status] ?? [
                            'bg' => 'bg-gray-50',
                            'text' => 'text-gray-700',
                            'border' => 'border-gray-100',
                            'dot' => 'bg-gray-500',
                        ];
                    @endphp
                    <span
                        class="inline-flex items-center px-4 py-2 rounded-xl {{ $cfg['bg'] }} {{ $cfg['text'] }} border {{ $cfg['border'] }} text-xs font-bold uppercase tracking-wider shadow-sm">
                        <span class="w-2 h-2 rounded-full {{ $cfg['dot'] }} mr-2"></span>
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>
            </div>

            {{-- OVERVIEW SECTION --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">

                    {{-- Order Reference ID --}}
                    <div class="p-6">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Order Reference ID') }}</label>
                        <p class="text-lg font-bold text-gray-900">
                            {{ $order->order_number ?? 'ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>

                    {{-- Order Date & Time --}}
                    <div class="p-6">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Order Date & Time') }}</label>
                        <p class="text-base font-medium text-gray-900">
                            {{ $order->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    {{-- Approved Date & Time --}}
                    <div class="p-6">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Approved Date & Time') }}</label>
                        @php
                            $approvedHistory = $order->statusHistory->where('status', 'approved')->first();
                        @endphp
                        <p class="text-base font-medium text-gray-900">
                            {{ $approvedHistory ? $approvedHistory->created_at->format('d M Y, h:i A') : '-' }}
                        </p>
                    </div>

                    {{-- Total Quantity Summary --}}
                    <div class="p-6 bg-gray-50/30">
                        <label
                            class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Total Quantity') }}</label>
                        <p class="text-lg font-bold text-brand-600">
                            {{ number_format($order->items->sum('quantity')) }}
                            <span class="text-xs text-gray-400 font-normal ml-1">{{ __('Units') }}</span>
                        </p>
                    </div>

                </div>
            </div>

            {{-- TABLE SECTION --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900 tracking-tight">{{ __('Order Details') }}</h3>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($order->items as $index => $orderItem)
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
                                                {{ $orderItem->snapshot_name ?? ($orderItem->item->name ?? __('Unknown Item')) }}
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
                                </tr>
                            @endforeach
                        </tbody>

                        {{-- TOTAL SUM FOOTER --}}
                        <tfoot class="bg-gray-50/50 border-t-2 border-gray-100">
                            <tr>
                                <td colspan="3" class="px-8 py-6 text-right">
                                    <span
                                        class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Total Sum of Stock Order Quantity') }}</span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="text-base font-bold text-brand-600">
                                        {{ number_format($order->items->sum('quantity')) }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>

            {{-- ✅ SUBMIT ORDER BUTTON --}}
            <form action="{{ route('reservation.submit') }}" method="POST" class="m-0 flex w-full justify-end">
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
    </div>
</x-app-layout>
