<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('Unified Order Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show"
                    class="mb-6 bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl shadow-sm flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-bold">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show"
                    class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl shadow-sm flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-bold">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @hasanyrole('admin|cs_leader')
                {{-- Dashboard View Toggle for Leadership --}}
                <div class="mb-6 flex justify-end">
                    <div class="inline-flex bg-white rounded-[1.5rem] p-1.5 shadow-sm border border-gray-100">
                        <a href="{{ route('dashboard', ['view' => 'mine']) }}"
                            class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all {{ $viewMode === 'mine' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-400 hover:text-gray-800 hover:bg-gray-50' }}">
                            {{ __('My Active Orders') }}
                        </a>
                        <a href="{{ route('dashboard', ['view' => 'all']) }}"
                            class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase transition-all {{ $viewMode === 'all' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:text-gray-800 hover:bg-gray-50' }}">
                            {{ __('All Team Orders') }}
                        </a>
                    </div>
                </div>
            @endhasanyrole

            {{-- Stats Container: Addendum Section 4.b --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                @foreach ($stats as $label => $count)
                    @php
                        // Map the label to the database status column value
                        $statusValue = match ($label) {
                            'Pending' => 'pending',
                            'Approved' => 'approved',
                            'In Transit' => 'in_transit',
                            'Delivered' => 'delivered',
                            'Cancelled' => 'cancelled',
                            default => '',
                        };

                        // Dynamically route based on whether we are looking at 'mine' or 'all'
                        $baseRoute = $viewMode === 'all' ? url('office/orders/all') : url('office/orders');

                        // Construct the final URL with the filter parameters if a specific status is clicked
                        $targetUrl = $statusValue ? "{$baseRoute}?search=&status={$statusValue}" : $baseRoute;
                    @endphp

                    <a href="{{ $targetUrl }}"
                        class="block bg-white p-6 rounded-3xl shadow-sm border border-gray-100 text-center transition-all hover:scale-105 hover:shadow-md hover:border-blue-200 {{ $viewMode === 'all' ? 'border-indigo-50 hover:border-indigo-300' : '' }}">
                        <p
                            class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1 transition-colors group-hover:text-blue-500">
                            {{ $label }}
                        </p>
                        <p class="text-3xl font-black {{ $viewMode === 'all' ? 'text-indigo-600' : 'text-gray-800' }}">
                            {{ number_format($count) }}
                        </p>
                    </a>
                @endforeach
            </div>

            {{-- Operational Shortcuts Container --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                    <h3 class="font-black text-gray-800 uppercase text-sm mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        {{ __('Quick Actions') }}
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- On-going Orders: Fulfills Section 5.a --}}
                        <a href="{{ route('office.orders.index') }}"
                            class="flex items-center justify-center bg-blue-600 text-white px-4 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-blue-700 transition shadow-sm">
                            {{ __('On-going Orders') }}
                        </a>

                        {{-- Claiming Queue: Fulfills Section 5.b --}}
                        <a href="{{ route('office.orders.queue') }}"
                            class="relative flex items-center justify-center bg-orange-600 text-white px-4 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-orange-700 transition shadow-lg shadow-orange-900/20">
                            {{ __('Claiming Queue') }}

                            @php
                                $queueCount = \App\Models\Order::where('status', 'pending')
                                    ->whereNull('handler_id')
                                    ->whereHas('user', function ($q) {
                                        $q->whereNull('assigned_cs_id');
                                    })
                                    ->count();
                            @endphp

                            {{-- AMENDED: Added @if check so it ONLY shows if count is greater than 0 --}}
                            @if ($queueCount > 0)
                                <span
                                    class="absolute -top-2 -right-2 bg-white text-orange-600 w-5 h-5 rounded-full flex items-center justify-center text-[9px] border-2 border-orange-600 shadow-sm animate-bounce">
                                    {{ $queueCount }}
                                </span>
                            @endif
                        </a>

                        {{-- My Claimed Orders: Fulfills Section 5.c --}}
                        <a href="{{ route('office.orders.history') }}"
                            class="flex items-center justify-center bg-gray-800 text-white px-4 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-gray-900 transition shadow-lg shadow-gray-900/20">
                            {{ __('My Claimed Orders') }}
                        </a>

                        {{-- Product Management: Fulfills Section 2.c.1 --}}
                        <a href="{{ route('items.index') }}"
                            class="flex items-center justify-center border-2 border-blue-600 text-blue-600 px-4 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-blue-50 transition">
                            {{ __('Manage Products') }}
                        </a>

                        @hasanyrole('admin|cs_leader')
                            <a href="{{ route('office.orders.cancellations') }}"
                                class="relative flex items-center justify-center bg-red-600 text-white px-4 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-red-700 transition shadow-lg shadow-red-900/20">
                                {{ __('Cancellation Requests') }}
                                @php $requestCount = \App\Models\Order::where('status', 'cancellation_requested')->count(); @endphp
                                @if ($requestCount > 0)
                                    <span
                                        class="absolute -top-2 -right-2 bg-white text-red-600 w-5 h-5 rounded-full flex items-center justify-center text-[9px] border-2 border-red-600 shadow-sm animate-bounce">
                                        {{ $requestCount }}
                                    </span>
                                @endif
                            </a>

                            <a href="{{ route('office.orders.all') }}"
                                class="flex items-center justify-center bg-indigo-600 text-white px-4 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                                {{ __('All System Orders') }}
                            </a>
                        @endhasanyrole
                    </div>
                </div>

                {{-- Active Session Context --}}
                <div
                    class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-4 bg-blue-50 rounded-2xl mr-4 text-blue-600">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">
                                {{ __('Active Staff') }}</p>
                            <p class="text-xl font-black text-gray-800 capitalize">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-blue-500 font-mono font-bold">
                                {{ auth()->user()->roles->first()->name }}</p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-[10px] text-gray-400 font-black uppercase mb-1">{{ __('Today') }}</p>
                        <p class="text-sm font-black text-gray-800">{{ now()->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
