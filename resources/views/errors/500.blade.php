<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Server Error') }} - {{ config('app.name', 'Maxtop') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>

<body class="font-sans antialiased text-gray-800 bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-xl w-full p-8 text-center">

        {{-- Warning Icon --}}
        <div class="mb-8 flex justify-center">
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        {{-- Error Message --}}
        <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tight mb-4">
            {{ __('System Error Encountered') }}
        </h1>

        <p class="text-gray-500 mb-8 leading-relaxed">
            {{ __('We apologize, but something went wrong on our servers. Please contact your system technician or IT support team to fix this issue.') }}
        </p>

        {{-- Info Box with Copy Feature --}}
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 inline-block text-left w-full max-w-sm">
            <h3 class="text-xs font-black uppercase text-gray-400 tracking-widest mb-2">{{ __('Support Information') }}
            </h3>
            <p class="text-sm font-bold text-gray-800 mb-4">Technical Support</p>

            <p class="text-[10px] uppercase font-black text-gray-400 mb-2">
                {{ __('Provide this exact time to support:') }}</p>

            {{-- Click to Copy Button --}}
            <button onclick="copyErrorTime(this)" data-time="{{ now()->format('Y-m-d H:i:s') }}"
                class="group w-full flex items-center justify-between bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-200 p-3 rounded-xl transition-all focus:outline-none">

                <span class="font-mono text-sm font-black text-gray-700 group-hover:text-red-600 transition-colors">
                    {{ now()->format('Y-m-d H:i:s') }}
                </span>

                <span
                    class="copy-text flex items-center gap-1 text-[10px] font-black uppercase text-gray-400 group-hover:text-red-500 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Copy
                </span>
            </button>
        </div>

        {{-- Return Button --}}
        <div>
            <a href="{{ url('/') }}"
                class="inline-block bg-gray-900 text-white font-black text-xs uppercase px-8 py-4 rounded-xl shadow-lg hover:bg-black transition-all">
                {{ __('Return to Homepage') }}
            </a>
        </div>

    </div>

    {{-- Safe JavaScript for Copying --}}
    <script>
        function copyErrorTime(button) {
            const timeStr = button.getAttribute('data-time');
            const textSpan = button.querySelector('.copy-text');

            navigator.clipboard.writeText(timeStr).then(() => {
                // Change UI to Green Success State
                textSpan.innerHTML =
                    `<svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg> <span class="text-green-500">COPIED!</span>`;
                button.classList.add('border-green-200', 'bg-green-50');

                // Revert back to original state after 3 seconds
                setTimeout(() => {
                    textSpan.innerHTML =
                        `<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg> Copy`;
                    button.classList.remove('border-green-200', 'bg-green-50');
                }, 3000);
            }).catch(err => {
                console.error('Failed to copy time', err);
            });
        }
    </script>
</body>

</html>
