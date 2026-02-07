<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('Cancellation Approval Queue') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('office.orders.cancellations') }}">
                    <x-filter-toolbar :placeholder="__('Search order # or customer...')" />
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-[2.5rem] border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Order Details') }}</th>
                            <th
                                class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Requested By') }}</th>
                            <th
                                class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Reason for Cancellation') }}</th>
                            <th
                                class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse ($requests as $order)
                            <tr class="hover:bg-red-50/30 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-gray-900">{{ $order->order_number }}</span>
                                        <span
                                            class="text-[10px] font-bold text-blue-600 uppercase">{{ $order->user->company->company_name ?? $order->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                                        <span
                                            class="text-[10px] font-black text-gray-600 uppercase">{{ $order->cancellationRequester->name ?? __('Unknown Staff') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-xs italic text-gray-500 leading-relaxed">
                                        "{{ $order->cancellation_request_reason }}"</p>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('office.orders.show', $order) }}"
                                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-[10px] font-black uppercase text-gray-600 transition">
                                            {{ __('Review Details') }}
                                        </a>
                                        <form action="{{ route('office.orders.cancel', $order) }}" method="POST"
                                            onsubmit="return confirm('Confirm permanent cancellation?');">
                                            @csrf
                                            {{-- ARCHITECTURE FIX: Pass the existing request reason to the controller --}}
                                            <input type="hidden" name="cancellation_reason"
                                                value="{{ $order->cancellation_request_reason }}">

                                            <button type="submit"
                                                class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-xl text-[10px] font-black uppercase text-white shadow-md transition-all">
                                                {{ __('Approve Cancel') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <p class="text-gray-400 font-black uppercase tracking-widest text-xs">
                                        {{ __('No pending cancellation requests found.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $requests->links() }}
        </div>
    </div>
</x-app-layout>
