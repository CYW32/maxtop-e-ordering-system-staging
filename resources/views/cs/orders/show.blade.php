<x-app-layout>
    <x-slot name="header">

    </x-slot>

    <div class="py-12">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Order Fulfillment') }}: <span
                        class="text-blue-600">{{ $order->order_number ?? __('DRAFT') }}</span>
                </h2>

                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ __('Place Order Date') }}:</span>
                    <span class="text-[10px] font-bold text-gray-700 uppercase italic">
                        {{ $placeOrderDate ? $placeOrderDate->format('d M Y | H:i') : __('Not Submitted') }}
                    </span>
                </div>

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

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. OWNERSHIP & STATE ALERTS [Backbone 4.a, 4.b] --}}
            @if ($order->status === 'draft')
                <div class="bg-amber-50 border-l-4 border-amber-400 p-6 rounded-2xl flex items-center gap-4 shadow-sm">
                    <svg class="w-8 h-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <p class="text-xs font-black text-amber-800 uppercase leading-relaxed tracking-tight">
                        {{ __('Customer Editing: This order is currently in DRAFT status. Operational actions (Approve/Ship) are locked until the customer re-submits for review.') }}
                    </p>
                </div>
            @elseif(
                $order->handler_id !== auth()->id() &&
                    !auth()->user()->hasAnyRole(['admin', 'cs_leader']))
                <div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-2xl flex items-center gap-4 shadow-sm">
                    <svg class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <p class="text-xs font-black text-blue-800 uppercase leading-relaxed tracking-tight">
                        {{ __('Read-Only Mode: You are not the current handler. Only ') . ($order->handler->name ?? __('Unassigned Staff')) . __(' can process this order.') }}
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- LEFT COLUMN: MANIFEST & LOGS --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- 2. ORDER ITEMS [Addendum 5.a] --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100">
                        <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                            <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest">
                                {{ __('Reservation Manifest') }}</h3>
                            <span class="text-[10px] font-black text-blue-600 uppercase">{{ $order->items->count() }}
                                {{ __('Line Items') }}</span>
                        </div>
                        <table class="min-w-full divide-y divide-gray-50">
                            <thead class="bg-gray-50/50">
                            <tr class="text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                <th class="px-10 py-6 text-left">{{ __('Product Entity') }}</th>
                                {{-- ARCHITECTURE FIX: Correct Packaging Unit Data Header --}}
                                <th class="px-6 py-6 text-center">{{ __('Packaging Unit (UOM)') }}</th>
                                <th class="px-10 py-6 text-right">{{ __('Ordered Qty') }}</th>
                                {{-- REMOVED: Unit Price column as requested --}}
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                            @forelse($order->items as $item)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-10 py-6">
                                        <div class="flex flex-col">
                                            {{-- Backbone 6.b: Prioritizing Snapshot Name --}}
                                            <span class="text-[11px] font-black text-gray-800 uppercase tracking-tight">
                                            {{ $item->snapshot_name ?? $item->item->name }}
                                        </span>
                                            <span class="text-[9px] font-mono font-bold text-blue-500 uppercase italic">
                                            SKU: {{ $item->item->sku }}
                                        </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        {{-- ARCHITECTURE FIX: Fetch Snapshot Packaging Unit [Backbone 6.b, 67] --}}
                                        <span class="px-4 py-2 bg-gray-50 rounded-xl text-[10px] font-black text-gray-600 uppercase border border-gray-100">
                                        {{ $item->snapshot_uom_name ?? ($item->uom->uom_name ?? __('UNIT')) }}
                                    </span>
                                    </td>
                                    <td class="px-10 py-6 text-right">
                                    <span class="text-sm font-mono font-black text-gray-900">
                                        {{ number_format($item->quantity, 0) }}
                                    </span>
                                    </td>
                                </tr>
                            @empty
                                {{-- ARCHITECTURE STANDARD: Professional Empty State --}}
                                <tr>
                                    <td colspan="3" class="px-10 py-20 text-center">
                                        <p class="text-[10px] font-black text-gray-300 uppercase italic tracking-widest">
                                            {{ __('No item records found for this order ID.') }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>

                            {{-- ARCHITECTURE FIX: Logistical Summary Footer --}}
                            <tfoot class="bg-gray-50/30">
                            <tr class="border-t-2 border-gray-100">
                                <td colspan="2" class="px-10 py-8 text-right">
                                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ __('Aggregated Sum Qty') }}</span>
                                </td>
                                <td class="px-10 py-8 text-right">
                                <span class="text-xl font-mono font-black text-blue-600">
                                    {{ number_format($order->items->sum('quantity'), 0) }}
                                </span>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- 3. INTERNAL OFFICE NOTES [Backbone 6.b] --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest">
                            {{ __('Internal Office Log (Private)') }}
                        </h3>

                        <div class="mb-6 p-5 bg-gray-50 rounded-[1.5rem] border border-dashed border-gray-200">
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-2">{{ __('Current Log') }}</p>
                            <div class="text-sm text-gray-700 whitespace-pre-wrap">
                                {{ $order->internal_notes ?: __('No internal notes recorded yet.') }}
                            </div>
                        </div>

                        <hr class="border-gray-100 mb-6">

                        <form action="{{ route('office.orders.updateStatus', $order) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="status" value="{{ $order->status }}">

                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-2">
                                    {{ __('Update Log') }}
                                </label>
                                <textarea
                                    name="internal_notes"
                                    rows="3"
                                    class="w-full border-gray-200 rounded-[1.5rem] text-sm focus:ring-blue-500 placeholder-gray-300 transition-all focus:border-blue-400"
                                    placeholder="{{ __('Type instructions or verification notes here...') }}"
                                >{{ old('internal_notes') }}</textarea>
                            </div>

                            <div class="flex justify-end">
                                <x-primary-button
                                    class="bg-gray-800 hover:bg-black py-2 px-6 rounded-xl text-[9px] font-black uppercase shadow-lg shadow-gray-200">
                                    {{ __('Update Internal Log') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>

                    {{-- 4. PRIVILEGED AUDIT TRAIL [Internal Order Audit Protocol] --}}
                    {{-- 5. REFACTORED: PRIVILEGED AUDIT TRAIL [Backbone 33.a, 31.f.2] --}}
                    @hasanyrole('admin|cs_leader')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100">
                        <div class="p-8 border-b border-gray-50 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ __('Internal Audit Trail: Status Transitions') }}</h3>
                        </div>
                        <div class="p-8 space-y-4">
                            @forelse ($order->statusHistory as $history)
                                <div class="flex flex-col p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-4">
                                            <span class="px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-[9px] font-black uppercase text-gray-600 shadow-sm">{{ str_replace('_', ' ', $history->status) }}</span>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-black text-gray-900 uppercase">{{ $history->changer->name }}</span>
                                                <span class="text-[8px] font-bold text-blue-500 uppercase">{{ $history->changer->roles->first()->name }}</span>
                                            </div>
                                        </div>
                                        <span class="text-[9px] font-black text-gray-400 uppercase">{{ $history->created_at->format('d M Y, H:i') }}</span>
                                    </div>

                                    {{-- ARCHITECTURE FIX: Cancellation Reason Injection in Audit Trail --}}
                                    @if($history->status === 'cancellation_requested' && $order->cancellation_request_reason)
                                        <div class="mt-2 p-3 bg-purple-100/50 rounded-xl border border-purple-100">
                                            <p class="text-[8px] font-black text-purple-400 uppercase mb-1 tracking-widest">{{ __('Cancel Reason') }}</p>
                                            <p class="text-[11px] font-bold text-purple-700 italic">"{{ $order->cancellation_request_reason }}"</p>
                                        </div>
                                    @endif

                                    @if($history->status === 'cancelled' && $order->cancellation_reason)
                                        <div class="mt-2 p-3 bg-red-100/50 rounded-xl border border-red-100">
                                            <p class="text-[8px] font-black text-red-400 uppercase mb-1 tracking-widest">{{ __('Manager Approval Note') }}</p>
                                            <p class="text-[11px] font-bold text-red-700 italic">"{{ $order->cancellation_reason }}"</p>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-[10px] text-gray-400 italic uppercase text-center py-4">{{ __('No status records found.') }}</p>
                            @endforelse
                        </div>
                    </div>
                    @endhasanyrole
                </div>

                {{-- RIGHT COLUMN: ENTITY INFO & ACTIONS --}}
                <div class="space-y-8">

                    {{-- 5. CUSTOMER ENTITY CARD [Addendum 1.a] --}}
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                        <h3
                            class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest border-b border-gray-50 pb-2">
                            {{ __('Business Information') }}</h3>
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-xs font-black uppercase shadow-lg shadow-blue-100">
                                {{ is_null($order->user->company?->parent_id) ? 'HQ' : 'BR' }}
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="text-sm font-black text-gray-900 uppercase leading-tight">{{ $order->user->company->company_name ?? $order->user->name }}</span>
                                <span class="text-[10px] font-mono text-blue-500 font-bold uppercase tracking-tighter">
                                    {{ $order->user->company->company_code ?? ($order->user->company->branch_code ?? 'N/A') }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <span
                                    class="text-[9px] font-black text-gray-400 uppercase block mb-1 tracking-tighter">{{ __('Delivery Address') }}</span>
                                <p class="text-xs font-bold text-gray-700 leading-relaxed italic">
                                    {{ $order->user->company->delivery_address ?? __('No address on file') }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span
                                        class="text-[9px] font-black text-gray-400 uppercase block mb-1">{{ __('PIC Name') }}</span>
                                    <p class="text-xs font-bold text-gray-700 uppercase">
                                        {{ $order->user->company->pic_name ?? $order->user->name }}</p>
                                </div>
                                <div>
                                    <span
                                        class="text-[9px] font-black text-gray-400 uppercase block mb-1">{{ __('PIC Contact') }}</span>
                                    <p class="text-xs font-bold text-gray-700">
                                        {{ $order->user->company->pic_phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 6. FULFILLMENT ACTIONS [Backbone 4, Addendum 4] --}}
                    @if (!in_array($order->status, ['completed', 'cancelled']))
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
                            <h3
                                class="text-[10px] font-black uppercase text-gray-400 tracking-widest border-b border-gray-50 pb-2">
                                {{ __('Fulfillment Protocol') }}</h3>

                            {{-- CLAIM [Backbone 5.b] --}}
                            @if (is_null($order->handler_id))
                                <form action="{{ route('office.orders.claim', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl text-xs font-black uppercase transition-all shadow-lg shadow-blue-100">
                                        {{ __('Claim This Order') }}
                                    </button>
                                </form>
                            @endif

                            {{-- APPROVAL [Backbone 4.c] --}}
                            @if ($order->status === 'pending' && $order->handler_id === auth()->id())
                                <form action="{{ route('office.orders.approve', $order) }}" method="POST"
                                    onsubmit="return confirm('{{ __('Are you sure? This will freeze item names and packaging rates for B2B historical accuracy.') }}');">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-2xl text-xs font-black uppercase transition-all shadow-lg shadow-green-900/40">
                                        {{ __('Approve & Snapshot') }}
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
                                            class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm"
                                            placeholder="e.g. DHL, Skynet">
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-gray-500 uppercase block mb-2">{{ __('Tracking Reference #') }}</label>
                                        <input type="text" name="tracking_number" required
                                            class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm"
                                            placeholder="e.g. TRK990122">
                                    </div>
                                    <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl text-xs font-black uppercase transition-all shadow-lg shadow-blue-100">
                                        {{ __('Mark In Transit') }}
                                    </button>
                                </form>
                            @endif

                            {{-- DANGER ZONE: CANCELLATION --}}
                            <div class="pt-6 border-t border-red-50 space-y-4">
                                @if ($order->hasPendingCancellationRequest())
                                    <div class="bg-purple-50 p-4 rounded-2xl border border-purple-100">
                                        <h4 class="text-[10px] font-black text-purple-700 uppercase mb-2">
                                            {{ __('Cancellation Pending Approval') }}</h4>
                                        <p class="text-[10px] text-purple-600 italic mb-4">
                                            "{{ $order->cancellation_request_reason }}"</p>

                                        @hasanyrole('admin|cs_leader')
                                            <form action="{{ route('office.orders.cancel', $order) }}" method="POST"
                                                class="space-y-3">
                                                @csrf
                                                <input type="text" name="cancellation_reason"
                                                    class="w-full bg-white border-purple-200 rounded-xl text-xs"
                                                    placeholder="{{ __('Final manager approval note...') }}" />
                                                <button type="submit"
                                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl text-[10px] font-black uppercase transition shadow-md">
                                                    {{ __('Confirm Cancellation') }}
                                                </button>
                                            </form>
                                        @else
                                            <div
                                                class="py-2 px-4 bg-purple-100 rounded-xl text-[9px] font-black text-purple-400 uppercase text-center italic tracking-widest">
                                                {{ __('Awaiting Manager Review') }}
                                            </div>
                                        @endhasanyrole
                                    </div>
                                @elseif(
                                    $order->handler_id === auth()->id() ||
                                        auth()->user()->hasAnyRole(['admin', 'cs_leader']))
                                    <form action="{{ route('office.orders.cancel', $order) }}" method="POST"
                                        class="space-y-3">
                                        @csrf
                                        <div>
                                            <input type="text" name="cancellation_reason" required
                                                class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm"
                                                placeholder="{{ __('Reason for cancellation (min 5 chars)...') }}" />
                                            <x-input-error :messages="$errors->get('cancellation_reason')" class="mt-1" />
                                        </div>
                                        <button type="submit"
                                            class="w-full border-2 border-red-100 text-red-500 hover:bg-red-50 py-3 rounded-2xl text-[10px] font-black uppercase transition-all">
                                            @if ($order->status === 'approved' && auth()->user()->hasRole('cs_staff'))
                                                {{ __('Request Order Cancellation') }}
                                            @else
                                                {{ __('Cancel Order') }}
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- 7. HANDOVER PROTOCOL [Backbone 5.d] --}}
                    @if (auth()->user()->hasAnyRole(['admin', 'cs_leader']) || $order->handler_id === auth()->id())
                        <div class="bg-orange-50 p-8 rounded-[2.5rem] border border-orange-100 shadow-sm space-y-4">
                            <h3
                                class="text-[10px] font-black uppercase text-orange-700 tracking-widest border-b border-orange-200/50 pb-2">
                                {{ __('Handler Assignment') }}</h3>
                            <form action="{{ route('office.orders.handover', $order) }}" method="POST"
                                class="space-y-4">
                                @csrf
                                <select name="new_handler_id"
                                    class="w-full border-orange-200 rounded-xl text-xs font-bold focus:ring-orange-500 bg-white">
                                    <option value="">{{ __('-- Transfer to Staff --') }}</option>
                                    @foreach ($eligibleStaff as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}
                                            ({{ str_replace('_', ' ', $staff->roles->first()->name) }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-md shadow-orange-200">
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
