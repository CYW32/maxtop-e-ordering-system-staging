<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Details: ') }} {{ $order->order_number }}
            </h2>
            <a href="{{ route('customer.orders.index') }}" class="text-sm text-blue-600 hover:underline">
                &larr; {{ __('Back to History') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Status Summary Card --}}
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">{{ __('Current Status') }}</p>
                    <p class="font-black text-blue-600 uppercase">{{ $order->status }}</p>
                </div>

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
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">{{ __('Item Description') }}
                            </th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-center">
                                {{ __('Qty') }}</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">
                                {{ __('Unit Price') }}</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">
                                {{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php $grandTotal = 0; @endphp
                        @foreach ($order->items as $item)
                            @php
                                $subtotal = $item->quantity * $item->price_at_order;
                                $grandTotal += $subtotal;
                            @endphp
                            <tr>
                                <td class="px-6 py-4">
                                    {{-- Fulfills Section 3C: Show Snapshot Name for Approved/Completed --}}
                                    <div class="font-bold text-gray-900">
                                        {{ in_array($order->status, ['pending', 'draft']) ? $item->item->name : $item->snapshot_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">SKU: {{ $item->item->sku }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right">RM {{ number_format($item->price_at_order, 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold">RM {{ number_format($subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-600">
                                {{ __('Order Total') }}</td>
                            <td class="px-6 py-4 text-right font-black text-xl text-blue-700">RM
                                {{ number_format($grandTotal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($order->cancellation_reason)
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <p class="text-xs font-bold text-red-700 uppercase">{{ __('Cancellation Reason') }}</p>
                    <p class="text-sm text-red-600 italic">"{{ $order->cancellation_reason }}"</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
