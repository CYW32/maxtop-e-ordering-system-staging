<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Fulfillment: ') }} {{ $order->order_number }}
            </h2>
            <span
                class="px-4 py-1 rounded-full text-sm font-black uppercase bg-blue-100 text-blue-800 border border-blue-300">
                {{ $order->status }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Ownership Alert: Section 5 Handover Logic --}}
            @if ($order->handler_id !== auth()->id())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <p class="text-sm text-yellow-700 font-bold">
                        {{ __('⚠️ READ-ONLY MODE: You are not the current handler. Only ') . ($order->handler->name ?? 'Unassigned') . __(' can process this order.') }}
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Left: Order Items & Snapshot Verification --}}
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="font-black text-gray-700 uppercase mb-4">{{ __('Order Items') }}</h3>
                        <table class="w-full text-left">
                            <thead class="border-b">
                                <tr>
                                    <th class="py-2 text-xs font-bold text-gray-500 uppercase">{{ __('Item Name') }}
                                    </th>
                                    <th class="py-2 text-xs font-bold text-gray-500 uppercase text-center">
                                        {{ __('Qty') }}</th>
                                    <th class="py-2 text-xs font-bold text-gray-500 uppercase text-right">
                                        {{ __('Price') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr class="border-b last:border-none">
                                        <td class="py-4">
                                            <div class="font-bold">
                                                {{ $order->status === 'pending' ? $item->item->name : $item->snapshot_name }}
                                            </div>
                                            <div class="text-xs text-gray-400">SKU: {{ $item->item->sku }}</div>
                                        </td>
                                        <td class="py-4 text-center">{{ $item->quantity }}</td>
                                        <td class="py-4 text-right">RM {{ number_format($item->price_at_order, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Internal Notes: Section 6 --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="font-black text-gray-700 uppercase mb-4">{{ __('Internal Office Notes (Private)') }}
                        </h3>
                        <form action="{{ route('office.orders.updateStatus', $order) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="{{ $order->status }}">
                            <textarea name="internal_notes" rows="4" class="w-full rounded-md border-gray-300"
                                placeholder="Add notes visible only to CS/Admin...">{{ $order->internal_notes }}</textarea>
                            <button type="submit"
                                class="mt-2 bg-gray-800 text-white px-4 py-2 rounded text-xs font-bold uppercase">
                                {{ __('Save Notes') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Right: Fulfillment Controls --}}
                <div class="space-y-6">
                    {{-- Customer Details Card --}}
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="font-black text-gray-700 uppercase mb-2">{{ __('Customer Info') }}</h3>
                        <p class="text-sm font-bold">{{ $order->user->details->company_name ?? $order->user->name }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $order->user->details->delivery_address }}</p>
                    </div>

                    {{-- Status Controls: Fulfills Section 4 lifecycle --}}
                    <div class="bg-gray-50 shadow rounded-lg p-6 border-t-4 border-blue-600">
                        <h3 class="font-black text-gray-700 uppercase mb-4">{{ __('Fulfillment Actions') }}</h3>

                        @if ($order->status === 'pending' && $order->handler_id === auth()->id())
                            <form action="{{ route('office.orders.approve', $order) }}" method="POST"
                                onsubmit="return confirm('Freeze item names and prices now?');">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-green-600 text-white py-3 rounded-lg font-black uppercase shadow hover:bg-green-700 mb-4">
                                    {{ __('✅ Approve & Snapshot') }} [14]
                                </button>
                            </form>
                        @endif

                        @if ($order->status === 'approved' && $order->handler_id === auth()->id())
                            <form action="{{ route('office.orders.updateStatus', $order) }}" method="POST"
                                class="space-y-4">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="in_transit">
                                <div>
                                    <label
                                        class="text-xs font-bold uppercase text-gray-500">{{ __('Carrier') }}</label>
                                    <input type="text" name="logistics_carrier" required
                                        class="w-full text-sm rounded border-gray-300">
                                </div>
                                <div>
                                    <label
                                        class="text-xs font-bold uppercase text-gray-500">{{ __('Tracking #') }}</label>
                                    <input type="text" name="tracking_number" required
                                        class="w-full text-sm rounded border-gray-300">
                                </div>
                                <button type="submit"
                                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-black uppercase shadow hover:bg-blue-700">
                                    {{ __('🚚 Mark In Transit') }} [12]
                                </button>
                            </form>
                        @endif

                        @if ($order->status !== 'completed' && $order->status !== 'cancelled' && $order->handler_id === auth()->id())
                            <hr class="my-4">
                            <h4 class="text-xs font-black text-red-600 uppercase mb-2">{{ __('Danger Zone') }}</h4>
                            <form action="{{ route('office.orders.cancel', $order) }}" method="POST">
                                @csrf
                                <textarea name="cancellation_reason" required class="w-full text-xs rounded border-gray-300 mb-2"
                                    placeholder="Mandatory cancellation reason..."></textarea>
                                <button type="submit"
                                    class="w-full border border-red-600 text-red-600 py-2 rounded text-xs font-black uppercase hover:bg-red-50">
                                    {{ __('Cancel Order') }}
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Handover Protocol: Section 5 --}}
                    @if (auth()->user()->hasAnyRole(['admin', 'cs_leader']) || $order->handler_id === auth()->id())
                        <div class="bg-white shadow rounded-lg p-6">
                            <h3 class="font-black text-gray-700 uppercase mb-4 text-xs">{{ __('Handover Ownership') }}
                            </h3>
                            <form action="{{ route('office.orders.handover', $order) }}" method="POST">
                                @csrf
                                <select name="new_handler_id" class="w-full text-sm rounded border-gray-300 mb-2">
                                    @foreach ($eligibleStaff as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}
                                            ({{ $staff->roles->first()->name }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="w-full bg-orange-100 text-orange-700 py-2 rounded text-xs font-black uppercase hover:bg-orange-200">
                                    {{ __('Transfer Handler') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
