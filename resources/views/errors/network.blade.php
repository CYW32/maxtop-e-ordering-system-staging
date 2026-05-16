<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Connection Failed') }} - {{ config('app.name', 'Maxtop') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>

<body class="font-sans antialiased text-gray-800 bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-xl w-full p-8 text-center">

        {{-- Offline Icon --}}
        <div class="mb-8 flex justify-center">
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
                </svg>
            </div>
        </div>

        {{-- Error Message --}}
        <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tight mb-4">
            {{ __('Connection Failed') }}
        </h1>

        <p class="text-gray-500 mb-8 leading-relaxed">
            {{ __('The system cannot establish a connection to the server. Please check your internet connection and try again.') }}
        </p>

        {{-- Return Button --}}
        <div>
            <a href="javascript:window.location.reload(true)"
                class="inline-block bg-gray-900 text-white font-black text-xs uppercase px-8 py-4 rounded-xl shadow-lg hover:bg-black transition-all mb-4">
                {{ __('Try Again') }}
            </a>
            <br>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}"
                class="text-xs font-bold text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">
                {{ __('Go Back') }}
            </a>
        </div>

    </div>
</body>

</html>
