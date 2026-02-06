<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Edit User Credentials') }}
                </h2>
                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mt-1">
                    {{ $user->name }} <span class="text-gray-400">({{ $user->login_id }})</span>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-10">
                    @csrf
                    @method('PUT')

                    <!-- SECTION 1: IDENTITY & SEARCHABLE BUSINESS LINK [Addendum 2.a & 3.b] -->
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <h3
                            class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            {{ __('System Identity & Business Assignment') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            {{-- Locked Login ID --}}
                            <div>
                                <x-input-label :value="__('Login ID (Locked)')"
                                    class="text-[10px] font-black uppercase text-gray-400" />
                                <div class="mt-2 flex items-center gap-2">
                                    <span
                                        class="text-sm font-mono font-bold text-gray-400 uppercase tracking-tighter">{{ $user->login_id }}</span>
                                    <svg class="w-3 h-3 text-gray-300" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Locked System Role --}}
                            <div>
                                <x-input-label :value="__('Account Role (Locked)')"
                                    class="text-[10px] font-black uppercase text-gray-400" />
                                <div class="mt-2">
                                    <span
                                        class="px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-black uppercase text-gray-400 shadow-sm inline-flex items-center gap-2">
                                        {{ str_replace('_', ' ', $user->roles->first()->name ?? 'N/A') }}
                                    </span>
                                </div>
                                {{-- Keep value for backend validation --}}
                                <input type="hidden" name="role" value="{{ $user->roles->first()->name ?? '' }}">
                            </div>

                            {{-- Searchable Company Assignment [Addendum 2.a & 3.d] --}}
                            <div x-data="{
                                selectedId: '{{ old('company_id', $user->company_id) }}',
                                companys: @js($companys)
                            }">
                                <x-input-label for="company_id" :value="__('Assigned Business (Searchable)')"
                                    class="text-[10px] font-black uppercase text-blue-600" />
                                <div class="mt-1 relative">
                                    <select name="company_id" id="company_id" x-model="selectedId"
                                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm">
                                        <option value="">{{ __('-- Select Business Entity --') }}</option>
                                        @foreach ($companys as $company)
                                            <option value="{{ $company->id }}">
                                                {{ $company->company_name }}
                                                ({{ $company->company_code ?? $company->branch_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                            </div>
                        </div>
                        <p class="mt-6 text-[10px] text-red-400 italic font-bold uppercase tracking-tight">
                            {{ __('Strategic Alert: Changing the company assignment immediately updates this user\'s product visibility based on the new Company\'s Catalog.') }}
                        </p>
                    </div>

                    <!-- SECTION 2: EDITABLE CREDENTIALS [Addendum 3.b] -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <h3 class="text-[10px] font-black uppercase text-blue-600 tracking-widest">
                                {{ __('Personal Information') }}</h3>

                            <div>
                                <x-input-label for="name" :value="__('Full Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    :value="old('name', $user->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email Address')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                    :value="old('email', $user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Account Access Status')" />
                                <select id="status" name="status"
                                    class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm font-bold uppercase">
                                    <option value="active"
                                        {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>
                                        {{ __('Active') }}</option>
                                    <option value="deactive"
                                        {{ old('status', $user->status) === 'deactive' ? 'selected' : '' }}>
                                        {{ __('Deactive') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-[10px] font-black uppercase text-blue-600 tracking-widest">
                                {{ __('Security & Office Assignment') }}</h3>

                            <div>
                                <x-input-label for="password" :value="__('Reset Password (Leave blank to keep current)')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                    autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            @can('reassign_customers')
                                <div>
                                    <x-input-label for="assigned_cs_id" :value="__('Responsible CS Representative')" />
                                    <select id="assigned_cs_id" name="assigned_cs_id"
                                        class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm text-sm">
                                        <option value="">{{ __('-- Unassigned --') }}</option>
                                        @foreach ($csStaffMembers as $cs)
                                            <option value="{{ $cs->id }}"
                                                {{ old('assigned_cs_id', $user->assigned_cs_id) == $cs->id ? 'selected' : '' }}>
                                                {{ $cs->name }} ({{ $cs->login_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endcan
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-6 border-t border-gray-100 gap-4">
                        <a href="{{ route('users.index') }}"
                            class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">{{ __('Cancel') }}</a>
                        <x-primary-button
                            class="bg-blue-600 hover:bg-blue-700 py-3 px-10 rounded-2xl shadow-lg shadow-blue-100 transition-all uppercase text-[10px] font-black">
                            {{ __('Save Profile Changes') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- DANGER ZONE: Fulfills Section 3.c Deletion Protection [9.c, 189] -->
            @can('edit_users')
                <div
                    class="bg-red-50 p-8 rounded-3xl border border-red-100 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-center md:text-left">
                        <h3 class="text-xs font-black uppercase text-red-600 tracking-widest mb-1">
                            {{ __('Dangerous: Permanent Removal') }}</h3>
                        <p class="text-xs text-red-400 font-medium">
                            {{ __('Deleting an account is only possible if the user has never placed an order.') }}</p>
                    </div>

                    @if ($user->canBeDeleted())
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                            onsubmit="return confirm('{{ __('Are you absolutely sure? This will permanently wipe this login credential.') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-8 py-2.5 rounded-xl text-[10px] font-black uppercase transition shadow-lg shadow-red-100">
                                {{ __('Confirm Hard Delete') }}
                            </button>
                        </form>
                    @else
                        <div class="flex items-center gap-3 px-6 py-2.5 bg-white border border-red-200 rounded-xl cursor-not-allowed opacity-60 shadow-sm"
                            title="{{ __('Data integrity lock active') }}">
                            <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span
                                class="text-[10px] font-black uppercase text-red-600">{{ __('Deletion Locked (Order History Found)') }}</span>
                        </div>
                    @endif
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
