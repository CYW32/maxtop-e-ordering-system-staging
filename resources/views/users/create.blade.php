<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ $parent ? __('Add Branch for ') . $parent->name : __('Create New User Credential') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Define restrictions and default role BEFORE the form starts --}}
            @php
                $isRestricted = auth()->user()->hasRole('cs_staff') || $parent;
                $defaultRole = $isRestricted ? 'customer' : '';
            @endphp

            {{-- Set the Alpine.js default role dynamically --}}
            <div x-data="{
                role: '{{ old('role', $defaultRole) }}'
            }"
                class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">

                {{-- ALERTS: Success and Error Messages --}}
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 p-4 rounded-2xl flex items-center gap-3 mb-6">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span
                            class="text-xs font-black uppercase text-red-800 tracking-wide">{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('users.store') }}" class="space-y-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- System Role Selection --}}
                        <div>
                            <x-input-label for="role" :value="__('System Access Role')" class="text-[10px] uppercase font-black" />

                            <select name="role" id="role" x-model="role"
                                class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm @if ($isRestricted) bg-gray-50 pointer-events-none @endif"
                                @if ($isRestricted) tabindex="-1" @endif required>

                                <option value="">{{ __('-- Select Account Type --') }}</option>

                                @foreach ($roles as $roleOption)
                                    @if (auth()->user()->hasRole('cs_staff') && in_array($roleOption->name, ['admin', 'cs_leader']))
                                        @continue
                                    @endif

                                    <option value="{{ $roleOption->name }}"
                                        {{ old('role') == $roleOption->name || ($parent && $roleOption->name == 'customer') ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $roleOption->name)) }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($isRestricted)
                                <input type="hidden" name="role" x-model="role">
                            @endif
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        {{-- Full Name --}}
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" class="text-[10px] uppercase font-black" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Login ID --}}
                        <div>
                            <x-input-label for="login_id" :value="__('Login ID (Unique Username)')" class="text-[10px] uppercase font-black" />
                            <x-text-input id="login_id" name="login_id" type="text"
                                class="mt-1 block w-full uppercase font-bold" :value="old('login_id')" required />
                            <x-input-error :messages="$errors->get('login_id')" class="mt-2" />
                        </div>

                        {{-- Email Address --}}
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" class="text-[10px] uppercase font-black" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Show ONLY if Role is 'customer' --}}
                    <div x-show="role === 'customer'" style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8 border-t border-gray-100 pt-8">
                            <div>
                                <div class="flex items-center justify-between">
                                    <x-input-label for="company_id" :value="__('Assigned Business Entity')"
                                        class="text-blue-800 font-black uppercase text-[10px]" />

                                    {{-- Live Sync Button --}}
                                    <button type="button" onclick="syncCompanies(this)"
                                        class="flex items-center text-[9px] font-black uppercase text-blue-500 hover:text-blue-700 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        {{ __('Sync Latest') }}
                                    </button>
                                </div>
                                <div class="mt-1">
                                    <select name="company_id" id="company_id"
                                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm">
                                        <option value="">{{ __('-- Select Business Entity --') }}</option>
                                        @foreach ($companys as $company)
                                            <option value="{{ $company->id }}"
                                                {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                                ({{ $company->company_code ?? $company->branch_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="assigned_cs_id" :value="__('Designated Customer Service Representative')"
                                    class="text-[10px] uppercase font-black" />

                                <select name="assigned_cs_id" id="assigned_cs_id"
                                    class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm text-sm @if ($isRestricted) bg-gray-50 pointer-events-none @endif"
                                    @if ($isRestricted) tabindex="-1" @endif>

                                    {{-- Only Admins/Leaders are allowed to leave this Unassigned --}}
                                    @if (!$isRestricted)
                                        <option value="">{{ __('-- Auto-Assign / Unassigned --') }}</option>
                                    @endif

                                    @foreach ($csStaffMembers as $cs)
                                        {{-- SECURITY FIX: If the user is a restricted CS Staff, hide all other CS reps from the list --}}
                                        @if ($isRestricted && $cs->id !== auth()->id())
                                            @continue
                                        @endif

                                        <option value="{{ $cs->id }}"
                                            {{ old('assigned_cs_id') == $cs->id || (!old('assigned_cs_id') && auth()->id() == $cs->id) ? 'selected' : '' }}>
                                            {{ $cs->name }} ({{ $cs->login_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- SMART PASSWORD SECTION --}}
                    <div x-data="passwordManager()"
                        class="bg-gray-50 p-6 rounded-3xl border border-gray-100 shadow-sm mt-8">
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Security Credentials') }}
                            </label>

                            {{-- Generate & Copy Button --}}
                            <button type="button" @click="generateAndCopy()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-100 text-indigo-700 hover:bg-indigo-200 hover:text-indigo-900 rounded-xl text-[10px] font-black uppercase transition-all shadow-sm">

                                {{-- Copy Icon --}}
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                {{-- Checkmark Icon --}}
                                <svg x-show="copied" style="display: none;" class="w-3.5 h-3.5 text-green-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>

                                <span
                                    x-text="copied ? '{{ __('Password Copied!') }}' : '{{ __('Generate Auto Password') }}'"></span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Main Password Input --}}
                            <div class="relative">
                                <label
                                    class="block text-[10px] uppercase font-black text-gray-700 mb-2">{{ __('Initial Password') }}
                                    <span class="text-red-500">*</span></label>
                                <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password"
                                    autocomplete="new-password" data-lpignore="true"
                                    class="w-full py-3 pl-4 pr-12 rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm font-bold text-gray-800 shadow-sm transition-shadow bg-white"
                                    placeholder="{{ __('Minimum 8 characters') }}" required>

                                {{-- Eye Icon Toggle --}}
                                <button type="button" @click="showPassword = !showPassword" tabindex="-1"
                                    class="absolute bottom-1 right-1 p-2 flex items-center text-gray-400 hover:text-blue-600 transition-colors">
                                    {{-- Eye Open Icon --}}
                                    <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{-- Eye Closed Icon --}}
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Confirm Password Input --}}
                            <div class="relative">
                                <label
                                    class="block text-[10px] uppercase font-black text-gray-700 mb-2">{{ __('Confirm Password') }}
                                    <span class="text-red-500">*</span></label>

                                <input :type="showPassword ? 'text' : 'password'" name="password_confirmation"
                                    x-model="password_confirmation" autocomplete="new-password" data-lpignore="true"
                                    class="w-full py-3 pl-4 pr-12 rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm font-bold text-gray-800 shadow-sm transition-shadow bg-white"
                                    placeholder="{{ __('Confirm Password') }}" required>
                            </div>
                        </div>

                        {{-- Laravel Validation Error --}}
                        <div class="mt-2">
                            <x-input-error :messages="$errors->get('password')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-8 border-t border-gray-100 gap-4">
                        {{-- FIXED: Everyone cancels to the unified users.index page --}}
                        <a href="{{ route('users.index') }}"
                            class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button
                            class="bg-blue-600 hover:bg-blue-700 py-4 px-10 rounded-2xl shadow-lg shadow-blue-200 transition-all uppercase text-[10px] font-black">
                            {{ __('Create User Credential') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        let companyTomSelect = null;

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('company_id')) {
                companyTomSelect = new TomSelect('#company_id', {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    placeholder: "-- Search and Select Business Entity --",
                    maxOptions: null
                });
            }
        });

        async function syncCompanies(btn) {
            const originalContent = btn.innerHTML;

            btn.innerHTML =
                `<svg class="animate-spin w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __('Syncing...') }}`;

            try {
                const response = await fetch("{{ route('users.create') }}");
                const html = await response.text();

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newOptions = doc.querySelectorAll('#company_id option');

                const currentSelection = companyTomSelect.getValue();

                companyTomSelect.clearOptions();
                newOptions.forEach(opt => {
                    if (opt.value) {
                        companyTomSelect.addOption({
                            value: opt.value,
                            text: opt.text
                        });
                    }
                });

                if (currentSelection) {
                    companyTomSelect.setValue(currentSelection, true);
                }

                btn.innerHTML =
                    `<svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> <span class="text-green-500">{{ __('Synced!') }}</span>`;

            } catch (error) {
                console.error(error);
                btn.innerHTML = `<span class="text-red-500">{{ __('Failed') }}</span>`;
            }

            setTimeout(() => {
                btn.innerHTML = originalContent;
            }, 2000);
        }
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('passwordManager', () => ({
                showPassword: true,
                password: '',
                password_confirmation: '',
                copied: false,

                generateAndCopy() {
                    const charset =
                        "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
                    let newPassword = "";
                    for (let i = 0; i < 12; i++) {
                        newPassword += charset.charAt(Math.floor(Math.random() * charset.length));
                    }

                    this.password = newPassword;
                    this.password_confirmation = newPassword;
                    this.showPassword = true;

                    navigator.clipboard.writeText(newPassword).then(() => {
                        this.copied = true;
                        setTimeout(() => {
                            this.copied = false;
                        }, 3000);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                    });
                }
            }))
        })
    </script>
</x-app-layout>
