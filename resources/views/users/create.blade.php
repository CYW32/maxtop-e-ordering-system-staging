<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ $parent ? __('Add Branch for ') . $parent->name : __('Create New User Credential') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- FIX: Define restrictions and default role BEFORE the form starts --}}
            @php
                $isRestricted = auth()->user()->hasRole('cs_staff') || $parent;
                $defaultRole = $isRestricted ? 'customer' : '';
            @endphp

            {{-- Set the Alpine.js default role dynamically --}}
            <div x-data="{
                role: '{{ old('role', $defaultRole) }}'
            }"
                class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">

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
                                <x-input-label for="company_id" :value="__('Assigned Business Entity')"
                                    class="text-blue-800 font-black uppercase text-[10px]" />
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-gray-100">
                        <div>
                            <x-input-label for="password" :value="__('Initial Password')" class="text-[10px] uppercase font-black" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')"
                                class="text-[10px] uppercase font-black" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                class="mt-1 block w-full" required />
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-8 border-t border-gray-100 gap-4">
                        <a href="{{ auth()->user()->hasAnyRole(['admin', 'cs_leader'])? route('users.index'): route('users.assigned') }}"
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

    <!-- TomSelect Library and Searchable Dropdown Initialization -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Target the specific company_id select element
            if (document.getElementById('company_id')) {
                new TomSelect('#company_id', {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    placeholder: "-- Search and Select Business Entity --",

                    // Set to null to remove the limit and let the user scroll all items
                    maxOptions: null
                });
            }
        });
    </script>
</x-app-layout>
