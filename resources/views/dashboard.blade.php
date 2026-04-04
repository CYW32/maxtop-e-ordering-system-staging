<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ✅ NOTIFICATION BLOCK --}}
            @if (session('success'))
                <div class="bg-brand-50 border border-brand-200 rounded-xl p-4 shadow-sm flex items-start mb-6">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 pt-0.5 w-full">
                        <p class="text-sm font-bold text-brand-800">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button" onclick="this.parentElement.parentElement.parentElement.remove()"
                                class="inline-flex rounded-lg p-1.5 text-brand-500 hover:bg-brand-100 focus:outline-none transition-colors">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            {{-- ✅ END NOTIFICATION BLOCK --}}

            <div class="relative bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-bl-full opacity-50"></div>

                <div class="relative px-8 py-12 flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-8 md:mb-0 max-w-2xl">
                        <div
                            class="inline-flex items-center px-3 py-1 rounded-full bg-brand-50 text-brand-600 text-sm font-medium mb-6 border border-brand-100">
                            <span class="flex w-2 h-2 rounded-full bg-brand-500 mr-2"></span>
                            Maxtop E-Ordering Portal
                        </div>
                        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                            {{ __('Welcome back, :name', ['name' => Auth::user()->name]) }}
                        </h1>
                        <p class="text-lg text-gray-500">
                            {{ __('Streamline your procurement process. Browse the latest catalog, manage your drafts, and track deliveries in real-time.') }}
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('customer.products.index') }}"
                            class="group relative inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white bg-brand-600 rounded-xl hover:bg-brand-700 transition-all duration-200 shadow-sm hover:shadow-brand-500/30 hover:shadow-lg overflow-hidden">
                            <span class="relative z-10 flex items-center">
                                <svg class="w-5 h-5 mr-2 -ml-1 transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z">
                                    </path>
                                </svg>
                                {{ __('New Order') }}
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <a href="{{ route('customer.products.index') }}"
                    class="group flex flex-col bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-500/10 transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Product Catalog') }}</h3>
                    <p class="text-gray-500 mb-6 flex-grow leading-relaxed">
                        {{ __('Explore our complete range of items, check stock levels, and add products to your cart.') }}
                    </p>
                    <div
                        class="flex items-center text-brand-600 font-semibold text-sm group-hover:translate-x-1 transition-transform duration-300">
                        {{ __('Browse Items') }}
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('reservation.index') }}"
                    class="group flex flex-col bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-500/10 transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('My Cart & Drafts') }}</h3>
                    <p class="text-gray-500 mb-6 flex-grow leading-relaxed">
                        {{ __('Review your selected items, modify quantities, and finalize your order submission.') }}
                    </p>
                    <div
                        class="flex items-center text-brand-600 font-semibold text-sm group-hover:translate-x-1 transition-transform duration-300">
                        {{ __('View Cart') }}
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </div>
                </a>

                <a href="{{ route('customer.orders.index') }}"
                    class="group flex flex-col bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-500/10 transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Order History') }}</h3>
                    <p class="text-gray-500 mb-6 flex-grow leading-relaxed">
                        {{ __('Track the status of active deliveries, view past orders, and download your invoices.') }}
                    </p>
                    <div
                        class="flex items-center text-brand-600 font-semibold text-sm group-hover:translate-x-1 transition-transform duration-300">
                        {{ __('Track Orders') }}
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
