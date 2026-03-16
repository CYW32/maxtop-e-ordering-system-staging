<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('On-going Orders (My Active Tasks)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mb-8">
            <x-filter-toolbar :placeholder="__('Search Order # or Customer Name...')" :showDates="true">
                <select name="status" class="text-xs border-gray-200 rounded-xl font-bold uppercase text-gray-600">
                    <option value="">{{ __('All Status') }}</option>
                    @foreach ($status as $statusName)
                        <option value="{{ $statusName }}" {{ request('status') == $statusName ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $statusName)) }}
                        </option>
                    @endforeach
                </select>
            </x-filter-toolbar>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($myOrders->isEmpty())
                    <p class="text-gray-500 italic">{{ __('You are not currently handling any active orders.') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Order #') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Customer') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">{{ __('Status') }}
                                    </th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600 text-right">
                                        {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($myOrders as $order)
                                    <tr class="border-b hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 font-mono text-sm">{{ $order->order_number }}</td>
                                        <td class="px-4 py-4 text-sm font-bold text-gray-800">{{ $order->user->name }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <span
                                                class="px-2 py-1 rounded-full text-[10px] font-black uppercase shadow-sm
                                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-blue-100 text-blue-800 border border-blue-300' }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                @if (is_null($order->handler_id))
                                                    <form action="{{ route('office.orders.claim', $order) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-black uppercase shadow-md">
                                                            {{ __('Claim My Customer Order') }}
                                                        </button>
                                                    </form>
                                                @else
                                                    @if (in_array($order->status, ['approved', 'in_transit', 'completed']))
                                                        {{-- 1. View Button (Opens in new tab to view only) --}}
                                                        <a href="{{ route('office.orders.pdf', $order) }}"
                                                            target="_blank"
                                                            class="inline-block bg-red-50 text-red-700 hover:bg-red-100 px-3 py-2 rounded-lg text-xs font-black uppercase transition border border-red-200"
                                                            title="View Order PDF">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-4 w-4 inline-block mr-1 -mt-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                            </svg>
                                                            {{ __('View') }}
                                                        </a>

                                                        {{-- 2. Download Button (Forces download to PC) --}}
                                                        <a href="{{ route('office.orders.stock-order', $order) }}"
                                                            class="inline-block bg-purple-50 text-purple-700 hover:bg-purple-100 px-3 py-2 rounded-lg text-xs font-black uppercase transition border border-purple-200"
                                                            title="Download Stock Order">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-4 w-4 inline-block mr-1 -mt-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            {{ __('Download') }}
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('office.orders.show', $order) }}"
                                                        class="inline-block bg-blue-50 text-blue-700 hover:bg-blue-700 hover:text-white px-4 py-2 rounded-lg text-xs font-black uppercase transition border border-blue-200">
                                                        {{ __('View & Process') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $myOrders->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
