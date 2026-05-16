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
                                    {{-- Use Alpine.js to isolate state for each row --}}
                                    <div class="flex justify-end gap-2" x-data="{ showModal: false }">
                                        <a href="{{ route('office.orders.show', $order) }}"
                                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-[10px] font-black uppercase text-gray-600 transition">
                                            {{ __('Review Details') }}
                                        </a>

                                        {{-- Trigger Modal Button --}}
                                        <button @click="showModal = true" type="button"
                                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-xl text-[10px] font-black uppercase text-white shadow-md shadow-purple-200 transition-all">
                                            {{ __('Process Request') }}
                                        </button>

                                        {{-- Interactive Processing Modal --}}
                                        <div x-show="showModal" style="display: none;"
                                            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm transition-opacity"
                                            x-transition:enter="ease-out duration-300"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="ease-in duration-200"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                                            <div @click.away="showModal = false"
                                                class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg overflow-hidden transform transition-all text-left"
                                                x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                                                <div class="p-8">
                                                    <div
                                                        class="flex justify-between items-center mb-6 border-b border-gray-50 pb-4">
                                                        <h3
                                                            class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                                                            <svg class="w-5 h-5 text-purple-500" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="2.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                            {{ __('Manager Decision Required') }}
                                                        </h3>
                                                        <button @click="showModal = false" type="button"
                                                            class="text-gray-400 hover:text-red-500 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor" stroke-width="2.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <div class="bg-gray-50 p-4 rounded-2xl mb-6">
                                                        <p
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                                            {{ __('Order ID') }}: <span
                                                                class="text-blue-600">{{ $order->order_number }}</span>
                                                        </p>
                                                        <p
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                                            {{ __('Requested By') }}: <span
                                                                class="text-gray-700">{{ $order->cancellationRequester->name ?? 'Staff' }}</span>
                                                        </p>
                                                        <p
                                                            class="text-[11px] font-bold text-gray-700 italic mt-2 border-l-2 border-purple-300 pl-3">
                                                            "{{ $order->cancellation_request_reason }}"</p>
                                                    </div>

                                                    <form action="{{ route('office.orders.cancel', $order) }}"
                                                        method="POST" class="space-y-6"
                                                        onsubmit="return confirm('{{ __('Are you sure you want to proceed with this decision?') }}');">
                                                        @csrf
                                                        <div>
                                                            <label
                                                                class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2">
                                                                {{ __('Manager Review Note (Optional)') }}
                                                            </label>
                                                            <textarea name="cancellation_reason" rows="3"
                                                                class="w-full border-gray-200 rounded-[1.5rem] bg-gray-50/50 text-sm font-bold text-gray-700 focus:ring-purple-500 focus:border-purple-400 transition-all placeholder-gray-400"
                                                                placeholder="{{ __('Type your reason for approving or rejecting here...') }}"></textarea>
                                                        </div>

                                                        <div class="flex flex-col md:flex-row justify-end gap-3 pt-2">
                                                            <button type="button" @click="showModal = false"
                                                                class="px-6 py-3 bg-white border border-gray-200 text-gray-500 hover:bg-gray-50 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                                                                {{ __('Cancel') }}
                                                            </button>

                                                            <button type="submit" name="action" value="reject"
                                                                class="px-6 py-3 bg-white border-2 border-purple-200 text-purple-600 hover:bg-purple-50 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                                                                {{ __('Reject Request') }}
                                                            </button>

                                                            <button type="submit" name="action" value="approve"
                                                                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-red-200">
                                                                {{ __('Approve Cancellation') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
