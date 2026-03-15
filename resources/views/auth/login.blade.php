<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maxtop: {
                            DEFAULT: '#597E38', /* 你的绿色 */
                            hover: '#46632c',
                        }
                    },
                    boxShadow: {
                        'soft': '0 20px 40px -15px rgba(0, 0, 0, 0.15)',
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-[#597E38] to-[#364e22] flex flex-col justify-center items-center p-6">
        
        <div class="mb-8 flex flex-col items-center animate-pulse">
            <img 
                src="https://maxtop.com.my/wp-content/themes/maxtop/assets/img/logo.svg" 
                alt="Maxtop" 
                class="h-20 w-auto object-contain filter brightness-0 invert" 
            />
        </div>

        <div class="bg-white w-full max-w-[400px] rounded-[30px] shadow-soft p-8 md:p-10 flex flex-col items-center relative z-10">
            
            <div class="text-center mb-8">
                <h2 class="text-xl font-bold text-gray-800">Welcome Back</h2>
                <p class="text-gray-400 text-xs tracking-widest uppercase mt-2 font-medium">B2B E-Ordering System</p>
            </div>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="w-full bg-red-50 text-red-500 text-xs p-3 rounded-xl text-center font-bold mb-4">
                    {{ __('Invalid credentials. Please try again.') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="w-full space-y-5">
                @csrf

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-400 ml-4 uppercase tracking-wider">{{ __('Username') }}</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-maxtop transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input 
                            id="login_id" 
                            type="text" 
                            name="login_id" 
                            value="{{ old('login_id') }}" 
                            class="w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-2xl text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-maxtop/50 transition-all font-medium outline-none"
                            placeholder="Enter username"
                            required 
                            autofocus 
                        />
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-400 ml-4 uppercase tracking-wider">{{ __('Password') }}</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-maxtop transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            class="w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-2xl text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-maxtop/50 transition-all font-medium outline-none"
                            placeholder="Enter password"
                            required 
                        />
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 px-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-maxtop focus:ring-maxtop h-4 w-4" name="remember">
                        <span class="ms-2 text-sm text-gray-500 font-medium">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-maxtop hover:text-green-700 font-bold transition-colors" href="{{ route('password.request') }}">
                            {{ __('Forgot?') }}
                        </a>
                    @endif
                </div>

                <button 
                 type="submit" 
                 class="w-full bg-maxtop hover:bg-[#46632c] text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-maxtop/30 hover:shadow-xl hover:shadow-maxtop/40 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 mt-4 ease-in-out">
                    {{ __('Sign In') }}
                </button>
            </form>
        </div>
        
        <div class="mt-8 text-white/40 text-xs font-medium tracking-wide">
            &copy; {{ date('Y') }} Maxtop. All rights reserved.
        </div>
    </div>
</body>
</html>