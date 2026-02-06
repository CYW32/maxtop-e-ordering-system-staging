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
                    {{-- Customer Details Card: Line 84 Refactoring [4] --}}
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <h4 class="text-[10px] font-black uppercase text-gray-400 mb-4 tracking-widest">
                            {{ __('Customer Info') }}</h4>

                        <div class="space-y-1">
                            {{-- Use null-safe operator to prevent Attempt to read property on null --}}
                            <p class="text-sm font-black text-gray-800 uppercase">
                                {{ $order->user->details?->company_name ?? $order->user->name }}
                            </p>

                            <p class="text-xs text-gray-500 leading-relaxed">
                                {{-- ARCHITECTURE FIX: Defensive check for delivery address --}}
                                @if ($order->user->details?->delivery_address)
                                    {{ $order->user->details->delivery_address }}
                                @else
                                    <span class="text-red-400 italic text-[10px] uppercase font-bold">
                                        {{ __('No business address on file') }}
                                    </span>
                                @endif
                            </p>
                        </div>
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
                                    {{ __('✅ Approve & Snapshot') }}
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

                        {{-- Danger Zone: Addendum Section 2 --}}
                        @if ($order->status !== 'completed' && $order->status !== 'cancelled' && $order->handler_id === auth()->id())
                            <div class="mt-8 pt-8 border-t border-gray-100">
                                <h3 class="text-[10px] font-black uppercase text-red-600 tracking-widest mb-4">
                                    {{ __('Danger Zone') }}</h3>

                                @if ($order->hasPendingCancellationRequest())
                                    {{-- Display Request for Everyone --}}
                                    <div class="bg-red-50 border border-red-100 p-6 rounded-2xl mb-4">
                                        <p class="text-[10px] font-black uppercase text-red-700 mb-2">
                                            {{ __('Cancellation Request Pending') }}</p>
                                        <p class="text-xs text-red-600 italic">
                                            "{{ $order->cancellation_request_reason }}"</p>
                                        <p class="text-[9px] text-red-400 mt-2 uppercase font-bold">—
                                            {{ __('Requested by') }} {{ $order->cancellationRequester->name }}</p>
                                    </div>

                                    {{-- Leader/Admin Approval Form [Addendum 2.b] --}}
                                    @hasanyrole('admin|cs_leader')
                                        <form action="{{ route('office.orders.cancel', $order) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="cancellation_reason"
                                                value="Approved Request: {{ $order->cancellation_request_reason }}">
                                            <button type="submit"
                                                class="w-full bg-red-600 text-white py-3 rounded-xl text-[10px] font-black uppercase hover:bg-red-700 shadow-lg transition">
                                                {{ __('Approve Cancellation Request') }}
                                            </button>
                                        </form>
                                    @else
                                        <div class="p-4 border-2 border-dashed border-gray-200 rounded-xl text-center">
                                            <p class="text-[10px] font-black uppercase text-gray-400">
                                                {{ __('Waiting for Management Approval') }}</p>
                                        </div>
                                    @endhasanyrole
                                @else
                                    {{-- Standard Cancellation Form [Backbone 4.f] --}}
                                    <form action="{{ route('office.orders.cancel', $order) }}" method="POST"
                                        class="space-y-3">
                                        @csrf
                                        <textarea name="cancellation_reason"
                                            class="w-full text-xs rounded-xl border-gray-200 focus:ring-red-500 focus:border-red-500"
                                            placeholder="{{ __('Provide mandatory reason for cancellation...') }}" required></textarea>

                                        <button type="submit"
                                            class="w-full border-2 border-red-600 text-red-600 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-red-50 transition">
                                            @if ($order->status === 'approved' && auth()->user()->hasRole('cs_staff'))
                                                {{ __('Submit Cancellation Request') }}
                                            @else
                                                {{ __('Cancel Order Permanently') }}
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
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
