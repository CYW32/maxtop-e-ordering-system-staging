<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex" x-data="{ open: false }">

        <aside class="bg-white border-r border-gray-100 w-64 min-h-screen hidden md:block">
            @include('layouts.navigation')
        </aside>

        <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black opacity-50 md:hidden"
            style="display: none;"></div>

        <aside :class="{ 'translate-x-0': open, '-translate-x-full': !open }"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 transition-transform duration-300 ease-in-out md:hidden transform -translate-x-full">
            @include('layouts.navigation')
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <header class="bg-white shadow flex justify-between items-center p-4 md:hidden">
                <button @click="open = ! open" class="text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
