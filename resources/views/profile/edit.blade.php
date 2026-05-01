<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('My Business Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. IDENTITY CARD: READ-ONLY [Backbone 3.b] --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100">
                <div class="p-10">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                        <div class="flex items-center gap-5">
                            <div
                                class="w-16 h-16 bg-blue-600 rounded-[1.5rem] flex items-center justify-center text-white text-xl font-black shadow-lg shadow-blue-100">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900">{{ $user->name }}</h3>
                                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest">
                                    {{ $user->roles->first()->name ?? 'Customer' }}</p>
                            </div>
                        </div>

                        {{-- COMMUNICATION PROTOCOL: Direct link to assigned CS Rep [12.a] --}}
                        <a href="mailto:{{ $user->csRepresentative->email ?? 'cs@maxtop.com' }}?subject=Profile Update Request: {{ $user->login_id }}"
                            class="bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-xl shadow-gray-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('Request Profile Modification') }}
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                        {{-- Security Data --}}
                        <div class="space-y-6">
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">{{ __('Login Identity (Locked)') }}</label>
                                <div
                                    class="bg-gray-50 border border-gray-100 px-4 py-3 rounded-xl font-mono text-sm font-bold text-gray-500 uppercase tracking-tighter">
                                    {{ $user->login_id }}
                                </div>
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">{{ __('Registered Email') }}</label>
                                <div class="text-sm font-bold text-gray-700">{{ $user->email }}</div>
                            </div>
                        </div>

                        {{-- Business Data --}}
                        <div class="space-y-6">
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">{{ __('Assigned Business Entity') }}</label>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-black text-gray-900">{{ $user->company->company_name ?? __('N/A') }}</span>
                                    <span
                                        class="text-[10px] font-mono font-black text-blue-500 uppercase tracking-tighter">
                                        {{ $user->company->company_code ?? ($user->company->branch_code ?? __('NO_CODE')) }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">{{ __('Responsible CS Representative') }}</label>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span
                                        class="text-sm font-bold text-gray-700">{{ $user->csRepresentative->name ?? __('Maxtop General Support') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-10 py-6 border-t border-gray-100 flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-relaxed">
                        {{ __('To maintain B2B account integrity, profile modifications must be verified and performed by your assigned representative.') }}
                    </p>
                </div>
            </div>

            {{-- NEW: BUSINESS ENTITY DETAILS (Location & Status) --}}
            @if ($user->company)
                @php $company = $user->company; @endphp
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                        <div>
                            <h3 class="text-xl font-black text-gray-900">{{ __('Location & Operations') }}</h3>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">
                                {{ __('Assigned fulfillment details and entity status') }}
                            </p>
                        </div>

                        {{-- Operational Status Badge --}}
                        <div>
                            @if ($company->status === 'active')
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100">
                                    <span class="flex w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                    {{ __('Active (Operational)') }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">
                                    <span class="flex w-2 h-2 rounded-full bg-red-500 mr-2 animate-pulse"></span>
                                    {{ __('Inactive (Suspended)') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 bg-gray-50/50 p-8 rounded-3xl border border-gray-100">
                        <div class="space-y-6">
                            {{-- HQ or Branch Tag --}}
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">{{ __('Organization Level') }}</label>
                                <div class="flex items-center">
                                    @if (is_null($company->parent_id))
                                        <span
                                            class="bg-blue-100 text-blue-800 text-[10px] font-black px-3 py-1 rounded-lg uppercase tracking-wider border border-blue-200">{{ __('Headquarters') }}</span>
                                    @else
                                        <span
                                            class="bg-purple-100 text-purple-800 text-[10px] font-black px-3 py-1 rounded-lg uppercase tracking-wider border border-purple-200">{{ __('Branch Office') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- PIC Data --}}
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">{{ __('Location Contact (PIC)') }}</label>
                                <div class="text-sm font-bold text-gray-700">
                                    {{ $company->pic_name ?? __('Not Assigned') }}
                                    @if ($company->pic_phone)
                                        <span
                                            class="block text-xs text-gray-500 font-mono mt-0.5">{{ $company->pic_phone }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            {{-- Specific Delivery Address --}}
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">{{ __('Fulfillment Address') }}</label>
                                <div
                                    class="text-sm font-bold text-gray-700 leading-relaxed bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                    {{ $company->delivery_address }}<br>
                                    @if ($company->postal_code || $company->city)
                                        {{ $company->postal_code }} {{ $company->city }}<br>
                                    @endif
                                    {{ $company->state }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 3. PASSWORD MANAGEMENT: EDITABLE [v1.4 Security Policy] --}}
            <div class="p-8 bg-white shadow-sm sm:rounded-[2.5rem] border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
