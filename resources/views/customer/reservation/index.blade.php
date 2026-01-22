<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Reservation Draft') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 text-green-600 font-bold">{{ session('success') }}</div>
                @endif

                @if (!$draft || $items->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500 mb-4">{{ __('Your reservation draft is currently empty.') }}</p>
                        <a href="{{ route('customer.products.index') }}" class="text-blue-600 underline">
                            {{ __('Browse Products to add items') }}
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="px-4 py-3 text-sm font-bold uppercase text-gray-600">{{ __('Item') }}
                                    </th>
                                    <th class="px-4 py-3 text-sm font-bold uppercase text-gray-600 text-center">
                                        {{ __('Quantity') }}</th>
                                    <th class="px-4 py-3 text-sm font-bold uppercase text-gray-600 text-right">
                                        {{ __('Unit Price') }}</th>
                                    <th class="px-4 py-3 text-sm font-bold uppercase text-gray-600 text-right">
                                        {{ __('Subtotal') }}</th>
                                    <th class="px-4 py-3 text-sm font-bold uppercase text-gray-600 text-right">
                                        {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = 0; @endphp
                                @foreach ($items as $orderItem)
                                    @php
                                        $subtotal = $orderItem->quantity * $orderItem->price_at_order;
                                        $grandTotal += $subtotal;
                                    @endphp
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-gray-900">{{ $orderItem->item->name }}</div>
                                            <div class="text-xs text-gray-500">SKU: {{ $orderItem->item->sku }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <form action="{{ route('reservation.update', $orderItem) }}" method="POST"
                                                class="flex items-center justify-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="quantity"
                                                    value="{{ $orderItem->quantity }}" min="1" max="999"
                                                    class="w-20 rounded border-gray-300 text-sm">
                                                <button type="submit"
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase">{{ __('Update') }}</button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-4 text-right">RM
                                            {{ number_format($orderItem->price_at_order, 2) }}</td>
                                        <td class="px-4 py-4 text-right font-semibold">RM
                                            {{ number_format($subtotal, 2) }}</td>
                                        <td class="px-4 py-4 text-right">
                                            <form action="{{ route('reservation.destroy', $orderItem) }}"
                                                method="POST" onsubmit="return confirm('Remove this item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 text-xs font-bold uppercase">{{ __('Remove') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-right font-bold text-lg text-gray-600">
                                        {{ __('Estimated Total:') }}</td>
                                    <td class="px-4 py-6 text-right font-black text-xl text-blue-700">RM
                                        {{ number_format($grandTotal, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <form action="{{ route('reservation.submit') }}" method="POST"
                            onsubmit="return confirm('Submit this reservation for review? You can still edit it while in Pending status.');">
                            @csrf
                            <button type="submit"
                                class="bg-green-600 text-white px-10 py-4 rounded-xl font-black text-lg hover:bg-green-700 transition shadow-lg">
                                {{ __('SUBMIT ORDER') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
