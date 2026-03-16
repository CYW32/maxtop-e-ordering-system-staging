<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 tracking-tight">
                    {{ __('Order History') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('Track your current deliveries and review past orders.') }}
                </p>
            </div>

            <a href="{{ route('customer.products.index') }}"
                class="inline-flex items-center px-5 py-2.5 bg-brand-600 text-white rounded-xl font-bold text-sm hover:bg-brand-700 transition-colors shadow-md shadow-brand-500/20 group">
                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('Create New Order') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- 🎛️ PREMIUM FILTER TOOLBAR --}}
            <form method="GET" action="{{ route('customer.orders.index') }}"
                class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md">

                {{-- Preserve per_page selection when applying other filters --}}
                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                    {{-- Search Input --}}
                    <div>
                        <label
                            class="block text-xs font-extrabold text-gray-500 uppercase tracking-wider mb-2">{{ __('Search') }}</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-brand-500 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Order ID..."
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 text-gray-900 rounded-xl text-sm font-semibold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-400 transition-all placeholder-gray-400 outline-none">
                        </div>
                    </div>

                    {{-- Status Dropdown --}}
                    <div>
                        <label
                            class="block text-xs font-extrabold text-gray-500 uppercase tracking-wider mb-2">{{ __('Status') }}</label>
                        <select name="status"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-700 rounded-xl text-sm font-semibold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-400 transition-all cursor-pointer outline-none">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                {{ __('Pending Review') }}</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                {{ __('Approved') }}</option>
                            <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>
                                {{ __('In Transit') }}</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                {{ __('Completed') }}</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                {{ __('Cancelled') }}</option>
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="md:col-span-2 grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-extrabold text-gray-500 uppercase tracking-wider mb-2">{{ __('From Date') }}</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-700 rounded-xl text-sm font-semibold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-400 transition-all cursor-pointer outline-none">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-extrabold text-gray-500 uppercase tracking-wider mb-2">{{ __('To Date') }}</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full px-4 py-3 bg-gray-50 border-gray-200 text-gray-700 rounded-xl text-sm font-semibold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-400 transition-all cursor-pointer outline-none">
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="mt-6 pt-6 border-t border-gray-100 flex justify-end items-center gap-4">
                    @if (request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('customer.orders.index') }}"
                            class="text-sm font-bold text-gray-500 hover:text-brand-600 transition-colors inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            {{ __('Clear Filters') }}
                        </a>
                    @endif
                    <button type="submit"
                        class="px-8 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl text-sm transition-all shadow-md shadow-brand-500/20 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        {{ __('Apply Filters') }}
                    </button>
                </div>
            </form>

            {{-- 📋 ORDER HISTORY TABLE --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50/80 border-b border-gray-100 text-xs font-extrabold text-gray-500 uppercase tracking-wider">
                                <th class="px-8 py-5">{{ __('Order Number') }}</th>
                                <th class="px-8 py-5">{{ __('Date Submitted') }}</th>
                                <th class="px-8 py-5">{{ __('Status') }}</th>
                                <th class="px-8 py-5 text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($orders as $order)
                                <tr class="hover:bg-brand-50/40 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="font-extrabold text-gray-900 text-base">
                                            {{ $order->order_number ?? 'ORD-' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-sm font-semibold text-gray-500">
                                        {{ $order->created_at->format('d M Y') }}
                                        <span
                                            class="text-gray-400 ml-1 font-normal">{{ $order->created_at->format('h:i A') }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        @if ($order->status == 'pending')
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold uppercase tracking-wider">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-2 shadow-sm"></span>
                                                {{ __('Pending') }}
                                            </span>
                                        @elseif($order->status == 'approved')
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-brand-50 text-brand-700 border border-brand-200 text-xs font-bold uppercase tracking-wider">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-brand-500 mr-2 shadow-sm"></span>
                                                {{ __('Approved') }}
                                            </span>
                                        @elseif($order->status == 'in_transit')
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-bold uppercase tracking-wider">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-2 shadow-sm"></span>
                                                {{ __('In Transit') }}
                                            </span>
                                        @elseif($order->status == 'completed')
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold uppercase tracking-wider">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2 shadow-sm"></span>
                                                {{ __('Completed') }}
                                            </span>
                                        @elseif($order->status == 'cancelled')
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs font-bold uppercase tracking-wider">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-red-500 mr-2 shadow-sm"></span>
                                                {{ __('Cancelled') }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 border border-gray-200 text-xs font-bold uppercase tracking-wider">
                                                {{ $order->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <a href="{{ route('customer.orders.show', $order) }}"
                                            class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 rounded-xl font-bold text-sm text-gray-700 hover:text-brand-700 hover:bg-brand-50 hover:border-brand-200 transition-all shadow-sm">
                                            {{ __('View Details') }}
                                            <svg class="w-4 h-4 ml-1.5 -mr-0.5 text-gray-400 group-hover:text-brand-500 transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-24 text-center">
                                        <div class="flex flex-col items-center max-w-sm mx-auto">
                                            <div
                                                class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 border border-gray-100 shadow-inner">
                                                <svg class="w-12 h-12 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                                {{ __('No orders found') }}</h3>
                                            <p class="text-base text-gray-500 mb-6">
                                                {{ __('We couldn\'t find any orders matching your current filters.') }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🎛️ CUSTOM PAGINATION & PER PAGE CONTROL --}}
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">

                {{-- Left Side: Page X/Y & Dropdown Selector --}}
                <div
                    class="flex items-center gap-5 bg-white px-5 py-3 rounded-2xl border border-gray-100 shadow-sm w-full md:w-auto justify-center md:justify-start">

                    {{-- Page 1 / 1 Indicator --}}
                    <span class="text-sm font-extrabold text-gray-700 whitespace-nowrap">
                        {{ __('Page') }} <span class="text-brand-600">{{ $orders->currentPage() }}</span> /
                        {{ max(1, $orders->lastPage()) }}
                    </span>

                    <div class="h-6 border-l border-gray-200"></div>

                    {{-- Per Page Mini Form --}}
                    <form method="GET" action="{{ route('customer.orders.index') }}"
                        class="flex items-center gap-3">

                        {{-- Keep all applied filters when changing the per page amount --}}
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if (request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        @if (request('date_from'))
                            <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                        @endif
                        @if (request('date_to'))
                            <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                        @endif

                        <label for="per_page"
                            class="text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:block">{{ __('Show:') }}</label>
                        <select name="per_page" id="per_page" onchange="this.form.submit()"
                            class="py-1.5 pl-4 pr-10 bg-gray-50 border-gray-200 text-gray-900 rounded-xl text-sm font-bold focus:ring-brand-500 focus:border-brand-500 transition-colors cursor-pointer outline-none">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10
                                {{ __('Orders') }}</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25
                                {{ __('Orders') }}</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100
                                {{ __('Orders') }}</option>
                        </select>
                    </form>

                </div>

                {{-- Right Side: Standard Laravel Next/Prev Buttons --}}
                <div class="w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                    {{ $orders->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
