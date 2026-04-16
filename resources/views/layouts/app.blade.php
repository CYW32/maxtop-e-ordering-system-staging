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
</body>

</html>
