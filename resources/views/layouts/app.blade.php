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

<body class="font-sans antialiased text-[#333333] bg-[#597E38]" x-data="{ sidebarOpen: true }">
    <div class="flex min-h-screen">

        <!-- Dynamic Dark Sidebar: Solid #597E38 with a subtle right border -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="bg-[#597E38] border-r border-white/10 shadow-lg hidden md:flex flex-col shrink-0 fixed inset-y-0 left-0 z-50 transition-all duration-300">
            @include('layouts.navigation')
        </aside>

        <!-- Main Content Area -->
        <main :class="sidebarOpen ? 'md:ml-64' : 'md:ml-20'"
            class="flex-1 p-6 md:p-10 min-w-0 flex flex-col transition-all duration-300">
            <div class="bg-[#FFFFFF] rounded-[16px] shadow-sm p-[40px] flex-1">
                {{ $slot }}
            </div>
        </main>

    </div>
</body>

</html>
