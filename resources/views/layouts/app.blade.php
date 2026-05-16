<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Maxtop B2B') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Hides collapsed elements before Alpine loads to prevent flickering -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased text-[#333333] bg-[#597E38]" x-data="{ sidebarOpen: window.innerWidth >= 768 }">
    <div class="flex min-h-screen">

        <div x-cloak x-show="sidebarOpen && window.innerWidth < 768" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-40 md:hidden transition-opacity"></div>

        <aside :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64 md:translate-x-0 md:w-20'"
            class="bg-[#597E38] border-r border-white/10 shadow-lg flex flex-col shrink-0 fixed inset-y-0 left-0 z-50 transition-all duration-300">
            @include('layouts.navigation')
        </aside>

        <main :class="sidebarOpen ? 'md:ml-64' : 'md:ml-20'"
            class="flex-1 p-4 md:p-10 min-w-0 flex flex-col transition-all duration-300 w-full overflow-x-hidden">

            <div class="md:hidden flex items-center justify-between mb-4 px-2">
                <img src="https://maxtop.com.my/wp-content/themes/maxtop/assets/img/logo.svg" alt="Maxtop Logo"
                    class="h-8 filter brightness-0 invert" />
                <button @click="sidebarOpen = true"
                    class="text-white hover:bg-white/10 p-2 rounded-lg transition-colors focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <div class="bg-[#FFFFFF] rounded-[16px] shadow-sm p-5 md:p-[40px] flex-1 flex flex-col">
                {{ $slot }}
            </div>
        </main>

    </div>

    {{-- Global Offline/No Internet Alert --}}
    <div x-data="{ offline: !navigator.onLine }" @online.window="offline = false" @offline.window="offline = true" x-cloak
        x-show="offline" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full"
        class="fixed bottom-0 left-0 right-0 z-[9999] p-4 flex justify-center pointer-events-none">

        <div
            class="bg-red-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 max-w-md w-full pointer-events-auto border-2 border-red-500/50">
            <div class="bg-white/20 p-2 rounded-full">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-black text-sm uppercase tracking-wider">{{ __('No Internet Connection') }}</h4>
                <p class="text-xs text-red-100 font-medium">
                    {{ __('You are currently offline. Please check your network.') }}</p>
            </div>
        </div>
    </div>
</body>

</html>
