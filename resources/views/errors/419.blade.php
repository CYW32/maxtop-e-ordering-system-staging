<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Page Expired') }} - {{ config('app.name', 'Maxtop') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>

<body class="font-sans antialiased text-gray-800 bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-xl w-full p-8 text-center">

        {{-- Timeout Icon --}}
        <div class="mb-8 flex justify-center">
            <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        {{-- Error Message --}}
        <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tight mb-4">
            {{ __('Session Expired') }}
        </h1>

        <p class="text-gray-500 mb-8 leading-relaxed">
            {{ __('For your security, your session has timed out due to inactivity. Please refresh the page or log in again to continue.') }}
        </p>

        {{-- Return Button --}}
        <div>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}"
                class="inline-block bg-orange-500 text-white font-black text-xs uppercase px-8 py-4 rounded-xl shadow-lg hover:bg-orange-600 transition-all mb-4">
                {{ __('Refresh Page') }}
            </a>
            <br>
            <a href="{{ route('login') }}"
                class="text-xs font-bold text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">
                {{ __('Go to Login') }}
            </a>
        </div>

    </div>
</body>

</html>
