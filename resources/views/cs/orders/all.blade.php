<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Master Order Registry') }}
            </h2>
            <span
                class="px-4 py-2 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg">
                {{ __('System-Wide Oversight') }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- THE SEARCH & FILTER TOOLBAR --}}
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('office.orders.all') }}"
                    class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 w-full relative">
                        <x-text-input name="search" value="{{ request('search') }}"
                            placeholder="{{ __('Search by Order #, Customer, or Handler...') }}"
                            class="w-full pl-10 pr-4 py-3 rounded-2xl border-gray-100 focus:ring-blue-500" />
                        <svg class="w-5 h-5 text-gray-300 absolute left-3 top-3.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <select name="status"
                        class="w-full md:w-48 border-gray-100 rounded-2xl text-xs font-black uppercase text-gray-500 focus:ring-blue-500">
                        <option value="">{{ __('All Status') }}</option>
                        @foreach ($status as $s)
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', $s) }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-md">
                        {{ __('Update Registry') }}
                    </button>

                    @if (request()->hasAny(['search', 'status']))
                        <a href="{{ route('office.orders.all') }}"
                            class="text-[10px] font-black uppercase text-red-400 hover:underline px-2">{{ __('Reset') }}</a>
                    @endif
                </form>
            </div>

            {{-- MASTER LIST TABLE --}}
            <div class="bg-white shadow-sm sm:rounded-[2.5rem] border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Order ID') }}</th>
                            <th
                                class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Business Entity') }}</th>
                            <th
                                class="px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Status') }}</th>
                            <th
                                class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Assigned Handler') }}</th>
                            <th
                                class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @foreach ($allOrders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-5">
                                    <span
                                        class="text-sm font-mono font-black text-blue-600 uppercase">{{ $order->order_number ?? '---' }}</span>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">
                                        {{ $order->created_at->format('d M Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        class="text-xs font-black text-gray-700 uppercase block">{{ $order->user->company->company_name ?? $order->user->name }}</span>
                                    <span
                                        class="text-[9px] font-mono text-gray-400 uppercase">{{ $order->user->company->company_code ?? ($order->user->company->branch_code ?? 'NO_CODE') }}</span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase border 
                                        {{ $order->status === 'pending' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : '' }}
                                        {{ $order->status === 'approved' ? 'bg-green-50 text-green-700 border-green-200' : '' }}
                                        {{ $order->status === 'in_transit' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                        {{ $order->status === 'completed' ? 'bg-gray-50 text-gray-700 border-gray-200' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-50 text-red-700 border-red-200' : '' }}
                                        {{ $order->status === 'draft' ? 'bg-gray-50 text-gray-400 border-gray-200' : '' }}">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    @if ($order->handler)
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-6 h-6 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 text-[9px] font-black">
                                                {{ substr($order->handler->name, 0, 1) }}
                                            </div>
                                            <span
                                                class="text-[10px] font-black text-gray-600 uppercase">{{ $order->handler->name }}</span>
                                        </div>
                                    @else
                                        <span
                                            class="text-[9px] font-black text-amber-500 uppercase italic">{{ __('Unassigned') }}</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('office.orders.show', $order) }}"
                                        class="inline-block bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-gray-50 transition shadow-sm">
                                        {{ __('Audit Order') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $allOrders->links() }}
        </div>
    </div>
</x-app-layout>
