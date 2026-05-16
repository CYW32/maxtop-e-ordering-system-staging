<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Edit User Credentials') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $user->name }}
                    ({{ $user->login_id }})</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- SECTION 1: IDENTITY & BUSINESS ASSIGNMENT --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">
                    <div
                        class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        {{ __('System Identity') }}{{ $user->hasRole('customer') ? __(' & Business Assignment') : '' }}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 {{ $user->hasRole('customer') ? 'mb-8' : '' }}">
                        {{-- Locked Login ID --}}
                        <div>
                            <x-input-label for="login_id" :value="__('Login Identity (Locked)')"
                                class="text-[10px] font-black uppercase text-gray-400" />
                            <div class="mt-2 flex items-center gap-2">
                                <span
                                    class="text-sm font-mono font-bold text-gray-400 uppercase tracking-tighter">{{ $user->login_id }}</span>
                                <svg class="w-3 h-3 text-gray-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Locked System Role --}}
                        <div>
                            <x-input-label for="role" :value="__('Account Authority (Locked)')"
                                class="text-[10px] font-black uppercase text-gray-400" />
                            <div class="mt-2">
                                <span
                                    class="px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-xs font-black uppercase text-gray-400 shadow-sm inline-flex items-center gap-2">
                                    {{ str_replace('_', ' ', $user->roles->first()->name ?? 'N/A') }}
                                </span>
                                <input type="hidden" name="role" value="{{ $user->roles->first()->name }}">
                            </div>
                        </div>
                    </div>

                    @if ($user->hasRole('customer'))
                        {{-- Searchable Company Assignment --}}
                        <div class="space-y-2 border-t border-gray-100 pt-6 mt-6">
                            <x-input-label for="company_id" :value="__('Assigned Business Entity')"
                                class="text-[10px] font-black uppercase text-blue-600" />
                            <select name="company_id" id="company_id"
                                class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm @if ($isAssignmentLocked) bg-gray-50 cursor-not-allowed @endif"
                                @if ($isAssignmentLocked) disabled @endif>
                                <option value="">{{ __('-- Select Business Entity --') }}</option>
                                @foreach ($companys as $company)
                                    <option value="{{ $company->id }}"
                                        {{ old('company_id', $user->company_id) == $company->id ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                        ({{ $company->company_code ?? $company->branch_code }})
                                    </option>
                                @endforeach
                            </select>

                            @if ($isAssignmentLocked)
                                <input type="hidden" name="company_id" value="{{ $user->company_id }}">
                                <p class="text-[9px] font-black text-amber-600 uppercase italic mt-2">
                                    {{ __('⚠️ Assignment Locked: Transaction history exists for this account. Relocation to a new business entity is restricted to maintain audit integrity.') }}
                                </p>
                            @else
                                <p class="mt-2 text-[9px] text-gray-400 italic uppercase">
                                    {{ __('Strategic Alert: Changing the company assignment immediately updates this user\'s product visibility based on the new Company\'s Catalog.') }}
                                </p>
                            @endif
                            <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                        </div>
                    @endif
                </div>

                {{-- SECTION 2: PERSONAL INFORMATION --}}
                <div class="bg-white shadow-sm sm:rounded-3xl border border-gray-100 p-8">
                    <div class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest">
                        {{ __('Personal Information') }}</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" class="text-[10px] font-black uppercase" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $user->name)" required />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Contact Email')" class="text-[10px] font-black uppercase" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email', $user->email)" required />
                        </div>
                    </div>
                    <div class="mt-6">
                        <x-input-label for="status" :value="__('Credential Status')" class="text-[10px] font-black uppercase" />
                        <select name="status"
                            class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm font-bold uppercase">
                            <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>
                                {{ __('Active') }}</option>
                            <option value="deactive"
                                {{ old('status', $user->status) === 'deactive' ? 'selected' : '' }}>
                                {{ __('Deactive') }}</option>
                        </select>
                    </div>
                </div>

                {{-- SECTION 3: SECURITY & OFFICE ASSIGNMENT --}}
                <div class="bg-white shadow-sm sm:rounded-3xl border border-gray-100 p-8">
                    <div class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest">
                        {{ __('Security & Control') }}</div>

                    @if ($user->hasRole('customer'))
                        @can('reassign_customers')
                            <div class="mb-8 border-b border-gray-100 pb-8">
                                <x-input-label for="assigned_cs_id" :value="__('Responsible CS Staff')"
                                    class="text-[10px] font-black uppercase" />
                                <select name="assigned_cs_id"
                                    class="mt-1 block w-full md:w-1/2 border-gray-300 rounded-xl shadow-sm text-sm">
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
                    @endif

                    {{-- SMART PASSWORD SECTION (Fixed Validation Bug + Added Toggle/Generator) --}}
                    <div x-data="passwordManager()" class="bg-gray-50 p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Reset Password (Optional)') }}
                            </label>

                            {{-- Generate & Copy Button --}}
                            <button type="button" @click="generateAndCopy()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-100 text-indigo-700 hover:bg-indigo-200 hover:text-indigo-900 rounded-xl text-[10px] font-black uppercase transition-all shadow-sm">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
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
                                    class="block text-[10px] uppercase font-black text-gray-700 mb-2">{{ __('New Password') }}</label>
                                <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password"
                                    autocomplete="new-password" data-lpignore="true"
                                    class="w-full py-3 pl-4 pr-12 rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm font-bold text-gray-800 shadow-sm transition-shadow bg-white"
                                    placeholder="{{ __('Leave blank to keep current') }}">

                                {{-- Eye Icon Toggle --}}
                                <button type="button" @click="showPassword = !showPassword" tabindex="-1"
                                    class="absolute bottom-1 right-1 p-2 flex items-center text-gray-400 hover:text-blue-600 transition-colors">
                                    <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Confirm Password Input (This was missing and caused the bug) --}}
                            <div class="relative">
                                <label
                                    class="block text-[10px] uppercase font-black text-gray-700 mb-2">{{ __('Confirm Password') }}</label>
                                <input :type="showPassword ? 'text' : 'password'" name="password_confirmation"
                                    x-model="password_confirmation" autocomplete="new-password" data-lpignore="true"
                                    class="w-full py-3 pl-4 pr-12 rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm font-bold text-gray-800 shadow-sm transition-shadow bg-white"
                                    placeholder="{{ __('Confirm new password') }}">
                            </div>
                        </div>

                        {{-- Validation Errors shown here if passwords don't match --}}
                        <div class="mt-2">
                            <x-input-error :messages="$errors->get('password')" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('users.index') }}"
                        class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">{{ __('Cancel') }}</a>
                    <x-primary-button
                        class="bg-blue-600 hover:bg-blue-700 py-3 px-10 rounded-2xl shadow-lg shadow-blue-100 transition-all uppercase text-[10px] font-black">
                        {{ __('Save Profile Changes') }}
                    </x-primary-button>
                </div>
            </form>

            @can('edit_users')
                <div
                    class="bg-red-50 p-8 rounded-3xl border border-red-100 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <h4 class="text-xs font-black text-red-600 uppercase tracking-widest">
                            {{ __('Dangerous: Permanent Removal') }}</h4>
                        <p class="text-[10px] text-red-400 mt-1 uppercase">
                            {{ __('Deleting an account is only possible if the user has never placed an order.') }}</p>
                    </div>
                    @if ($user->canBeDeleted())
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                            onsubmit="return confirm('{{ __('Are you absolutely sure? This will permanently wipe this login credential.') }}');">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-8 py-2.5 rounded-xl text-[10px] font-black uppercase transition shadow-lg shadow-red-100">
                                {{ __('Confirm Hard Delete') }}
                            </button>
                        </form>
                    @else
                        <div class="flex items-center gap-2" title="{{ __('Data integrity lock active') }}">
                            <svg class="w-4 h-4 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path
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

    {{-- Alpine.js Password Logic --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('passwordManager', () => ({
                showPassword: false, // Default hidden for edit page
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
