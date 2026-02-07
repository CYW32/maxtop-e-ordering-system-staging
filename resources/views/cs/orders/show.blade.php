<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Order Fulfillment') }}: <span
                        class="text-blue-600">{{ $order->order_number ?? __('DRAFT') }}</span>
                </h2>
                <div class="mt-1 flex items-center gap-2">
                    <span
                        class="px-3 py-1 rounded-full text-[10px] font-black uppercase border shadow-sm 
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : '' }}
                        {{ $order->status === 'approved' ? 'bg-green-100 text-green-800 border-green-300' : '' }}
                        {{ $order->status === 'in_transit' ? 'bg-blue-100 text-blue-800 border-blue-300' : '' }}
                        {{ $order->status === 'completed' ? 'bg-gray-100 text-gray-800 border-gray-300' : '' }}
                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 border-red-300' : '' }}
                        {{ $order->status === 'draft' ? 'bg-gray-50 text-gray-500 border-gray-200' : '' }}
                        {{ $order->status === 'cancellation_requested' ? 'bg-purple-100 text-purple-800 border-purple-300' : '' }}">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>
            </div>
            <a href="{{ route('office.orders.index') }}"
                class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back to Workspace') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. OWNERSHIP & STATE ALERTS [Backbone 4.a, 4.b] --}}
            @if ($order->status === 'draft')
                <div class="p-6 bg-amber-50 border border-amber-200 rounded-[2rem] flex items-center gap-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black text-amber-700 uppercase tracking-tight">
                        {{ __('Customer Editing: This order is currently in DRAFT status. Operational actions (Approve/Ship) are locked until the customer re-submits for review.') }}
                    </p>
                </div>
            @elseif (
                $order->handler_id !== auth()->id() &&
                    !auth()->user()->hasAnyRole(['admin', 'cs_leader']))
                <div class="p-6 bg-gray-100 border border-gray-200 rounded-[2rem] flex items-center gap-4 opacity-75">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black text-gray-500 uppercase tracking-tight">
                        {{ __('Read-Only Mode: You are not the current handler. Only ') . ($order->handler->name ?? __('Unassigned Staff')) . __(' can process this order.') }}
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- LEFT COLUMN: ORDER ITEMS & NOTES --}}
                <div class="lg:col-span-2 space-y-8">
                    {{-- 2. ORDER ITEMS [Addendum 5.a, 101] --}}
                    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">
                                {{ __('Reservation Manifest') }}</h3>
                        </div>
                        <table class="min-w-full divide-y divide-gray-50">
                            <thead class="bg-white">
                                <tr>
                                    <th
                                        class="px-8 py-4 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Product Details') }}</th>
                                    <th
                                        class="px-6 py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Packaging Unit') }}</th>
                                    <th
                                        class="px-6 py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Qty') }}</th>
                                    <th
                                        class="px-8 py-4 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Unit Price') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @foreach ($order->items as $item)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[10px] font-mono font-black text-blue-600 uppercase">{{ $item->item->sku }}</span>
                                                <span class="text-sm font-bold text-gray-900 leading-tight">
                                                    {{ in_array($order->status, ['draft', 'pending']) ? $item->item->name : $item->snapshot_name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span
                                                class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $item->uom_id ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-gray-50 text-gray-400' }}">
                                                {{ $item->uom?->uom_name ?? __('Individual Unit') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-center font-black text-gray-700">
                                            {{ $item->quantity }}</td>
                                        <td class="px-8 py-5 text-right font-mono text-xs font-black text-gray-400">
                                            RM {{ number_format($item->price_at_order, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. INTERNAL OFFICE NOTES [Backbone 6.b] --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                        <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] mb-6">
                            {{ __('Internal Office Log (Private)') }}</h3>
                        <form action="{{ route('office.orders.updateStatus', $order) }}" method="POST"
                            class="space-y-4">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="{{ $order->status }}">
                            <textarea name="internal_notes" rows="4"
                                class="w-full border-gray-200 rounded-[1.5rem] text-sm focus:ring-blue-500 placeholder-gray-300"
                                placeholder="{{ __('Add staff-only comments, delivery instructions, or verification notes...') }}">{{ old('internal_notes', $order->internal_notes) }}</textarea>
                            <div class="flex justify-end">
                                <x-primary-button
                                    class="bg-gray-800 hover:bg-black py-2 px-6 rounded-xl text-[9px] font-black uppercase">
                                    {{ __('Update Internal Log') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- RIGHT COLUMN: CUSTOMER INFO & ACTIONS --}}
                <div class="space-y-8">
                    {{-- 4. CUSTOMER ENTITY CARD [Addendum 1.a] --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                        <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] mb-6">
                            {{ __('Business Information') }}</h3>
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-xs font-black uppercase">
                                {{ is_null($order->user->company?->parent_id) ? 'HQ' : 'BR' }}
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="text-sm font-black text-gray-900 uppercase">{{ $order->user->company->company_name ?? $order->user->name }}</span>
                                <span
                                    class="text-[10px] font-mono text-blue-500 font-bold uppercase">{{ $order->user->company->company_code ?? ($order->user->company->branch_code ?? 'N/A') }}</span>
                            </div>
                        </div>
                        <div class="space-y-4 pt-4 border-t border-gray-50">
                            <div>
                                <label
                                    class="text-[9px] font-black text-gray-400 uppercase block mb-1">{{ __('Delivery Address') }}</label>
                                <p class="text-xs font-bold text-gray-600 leading-relaxed">
                                    {{ $order->user->company->delivery_address ?? __('No address on file') }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="text-[9px] font-black text-gray-400 uppercase block mb-1">{{ __('PIC Name') }}</label>
                                    <p class="text-xs font-bold text-gray-600">
                                        {{ $order->user->company->pic_name ?? $order->user->name }}</p>
                                </div>
                                <div>
                                    <label
                                        class="text-[9px] font-black text-gray-400 uppercase block mb-1">{{ __('PIC Contact') }}</label>
                                    <p class="text-xs font-bold text-gray-600">
                                        {{ $order->user->company->pic_phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. FULFILLMENT ACTIONS [Backbone 4, Addendum 4] --}}
                    @if (!in_array($order->status, ['completed', 'cancelled']))
                        <div class="bg-gray-900 p-8 rounded-[2.5rem] shadow-xl text-white">
                            <h3 class="text-[10px] font-black uppercase text-blue-400 tracking-[0.2em] mb-8">
                                {{ __('Fulfillment Protocol') }}</h3>

                            {{-- APPROVAL [Backbone 4.c] --}}
                            @if ($order->status === 'pending' && $order->handler_id === auth()->id())
                                <form action="{{ route('office.orders.approve', $order) }}" method="POST"
                                    onsubmit="return confirm('{{ __('Are you sure? This will freeze item names and packaging rates for B2B historical accuracy.') }}');">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 py-4 rounded-2xl text-xs font-black uppercase transition-all shadow-lg shadow-green-900/50">
                                        {{ __('Approve & Snapshot Order') }}
                                    </button>
                                </form>
                            @endif

                            {{-- DISPATCH [Backbone 4.d] --}}
                            @if ($order->status === 'approved' && $order->handler_id === auth()->id())
                                <form action="{{ route('office.orders.updateStatus', $order) }}" method="POST"
                                    class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="in_transit">
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-500 uppercase block mb-2">{{ __('Logistics Provider') }}</label>
                                        <input type="text" name="logistics_carrier" required
                                            class="w-full bg-gray-800 border-gray-700 rounded-xl text-sm focus:ring-blue-500"
                                            placeholder="e.g. DHL, Skynet">
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-500 uppercase block mb-2">{{ __('Tracking Reference #') }}</label>
                                        <input type="text" name="tracking_number" required
                                            class="w-full bg-gray-800 border-gray-700 rounded-xl text-sm focus:ring-blue-500"
                                            placeholder="e.g. TRK990122">
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 py-4 rounded-2xl text-xs font-black uppercase transition-all shadow-lg shadow-blue-900/50">
                                        {{ __('Dispatch / Mark In Transit') }}
                                    </button>
                                </form>
                            @endif

                            {{-- DANGER ZONE: CANCELLATION WORKFLOW [Addendum 4] --}}
                            <div class="mt-8 pt-8 border-t border-gray-800 space-y-4">
                                @if ($order->hasPendingCancellationRequest())
                                    <div class="bg-red-900/20 border border-red-900/50 p-4 rounded-2xl text-center">
                                        <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-2">
                                            {{ __('Cancellation Pending Approval') }}</p>
                                        <p class="text-xs italic text-gray-400 mb-4">
                                            "{{ $order->cancellation_request_reason }}"</p>

                                        @hasanyrole('admin|cs_leader')
                                            <form action="{{ route('office.orders.cancel', $order) }}" method="POST"
                                                class="space-y-3">
                                                @csrf
                                                {{-- ARCHITECTURE FIX: Allow manager to add a final note or use the requester's reason --}}
                                                <x-text-input name="cancellation_reason"
                                                    class="w-full bg-gray-800 border-gray-700 text-xs text-white"
                                                    placeholder="{{ __('Optional: Add manager approval note...') }}" />

                                                <button type="submit"
                                                    class="w-full bg-red-600 hover:bg-red-700 py-2.5 rounded-xl text-[10px] font-black uppercase transition shadow-md">
                                                    {{ __('Approve Cancellation') }}
                                                </button>
                                            </form>
                                        @else
                                            <div
                                                class="py-2 px-4 bg-gray-800 rounded-xl text-[9px] font-black text-gray-500 uppercase">
                                                {{ __('Awaiting Manager Review') }}
                                            </div>
                                        @endhasanyrole
                                    </div>
                                @elseif (
                                    $order->handler_id === auth()->id() ||
                                        auth()->user()->hasAnyRole(['admin', 'cs_leader']))
                                    <form action="{{ route('office.orders.cancel', $order) }}" method="POST"
                                        class="space-y-3">
                                        @csrf
                                        <x-text-input name="cancellation_reason" required
                                            class="w-full bg-gray-800 border-gray-700 text-sm placeholder-gray-600"
                                            placeholder="{{ __('Mandatory cancellation reason...') }}" />

                                        <button type="submit"
                                            class="w-full border-2 border-red-900/50 text-red-500/80 hover:bg-red-900/20 py-3 rounded-2xl text-[10px] font-black uppercase transition-all">
                                            @if ($order->status === 'approved' && auth()->user()->hasRole('cs_staff'))
                                                {{-- Fulfills Addendum 4.a: CS Staff only requests --}}
                                                {{ __('Request Order Cancellation') }}
                                            @elseif (auth()->user()->hasAnyRole(['admin', 'cs_leader']))
                                                {{-- Managers see confirmation --}}
                                                {{ __('Confirm Order Cancellation') }}
                                            @else
                                                {{-- Fulfills Request: csstaff001 on non-approved orders --}}
                                                {{ __('Cancel Order') }}
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- 6. HANDOVER PROTOCOL [Backbone 5.d] --}}
                    @if (auth()->user()->hasAnyRole(['admin', 'cs_leader']) || $order->handler_id === auth()->id())
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                            <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] mb-6">
                                {{ __('Handler Assignment') }}</h3>
                            <form action="{{ route('office.orders.handover', $order) }}" method="POST"
                                class="space-y-4">
                                @csrf
                                <select name="new_handler_id"
                                    class="w-full border-gray-200 rounded-xl text-xs font-bold focus:ring-orange-500">
                                    <option value="">{{ __('-- Transfer to Staff --') }}</option>
                                    @foreach ($eligibleStaff as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}
                                            ({{ str_replace('_', ' ', $staff->roles->first()->name) }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="w-full bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white py-3 rounded-2xl text-[10px] font-black uppercase transition-all">
                                    {{ __('Handover Authority') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
