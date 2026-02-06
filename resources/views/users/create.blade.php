<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ $parent ? __('Add Branch for ') . $parent->name : __('Create New User Credential') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- ARCHITECTURE: Alpine state manages role-based conditional UI and searchable business mapping --}}
            <div x-data="{
                role: '{{ old('role', $parent ? 'customer' : '') }}',
                selectedId: '{{ old('company_id') }}',
                companys: @js($companys)
            }"
                class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">

                <form method="POST" action="{{ route('users.store') }}" class="space-y-10">
                    @csrf

                    <!-- SECTION 1: ACCOUNT TYPE & CREDENTIALS [Addendum 3.b] -->
                    <div class="space-y-6">
                        <h3 class="text-[10px] font-black uppercase text-blue-600 mb-6 tracking-widest">
                            {{ __('Step 1: Identity & Role') }}</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- System Role Selection --}}
                            <div>
                                <x-input-label for="role" :value="__('System Access Role')"
                                    class="text-[10px] uppercase font-black" />
                                @php $isRestricted = auth()->user()->hasRole('cs_staff') || $parent; @endphp
                                <select name="role" id="role" x-model="role"
                                    class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm @if ($isRestricted) bg-gray-50 cursor-not-allowed @endif"
                                    @if ($isRestricted) readonly @endif required>
                                    <option value="">{{ __('-- Select Account Type --') }}</option>
                                    @foreach ($roles as $roleOption)
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
                                <x-input-label for="name" :value="__('Full Name')"
                                    class="text-[10px] uppercase font-black" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Login ID --}}
                            <div>
                                <x-input-label for="login_id" :value="__('Login ID (Unique Username)')"
                                    class="text-[10px] uppercase font-black" />
                                <x-text-input id="login_id" name="login_id" type="text"
                                    class="mt-1 block w-full uppercase font-bold" :value="old('login_id')" required />
                                <x-input-error :messages="$errors->get('login_id')" class="mt-2" />
                            </div>

                            {{-- Email Address --}}
                            <div>
                                <x-input-label for="email" :value="__('Email Address')"
                                    class="text-[10px] uppercase font-black" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                    :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: SEARCHABLE BUSINESS ENTITY [Addendum 2.a, 3.d] -->
                    {{-- Fulfills requirement: Show ONLY if Role is 'customer' --}}
                    <div x-show="role === 'customer'" x-transition.duration.400ms
                        class="p-8 bg-blue-50 rounded-3xl border border-blue-100 space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-[10px] font-black uppercase text-blue-600 tracking-widest">
                                {{ __('Step 2: Business Assignment') }}</h3>
                            <span
                                class="px-2 py-1 bg-blue-600 text-white text-[8px] font-black uppercase rounded">{{ __('Required for Customers') }}</span>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            {{-- Searchable Company Logic --}}
                            <div>
                                <x-input-label for="company_search" :value="__('Assign to Business Entity (Search Name or Code)')"
                                    class="text-blue-800 font-black uppercase text-[10px]" />
                                <div class="mt-2 relative">
                                    <input list="company-list" id="company_search" name="company_name_display"
                                        class="w-full border-blue-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm"
                                        placeholder="Type to search e.g. 'MaxTop HQ' or 'BR-001'..."
                                        @change="val = $event.target.value; let match = companys.find(c => c.company_name === val || (c.company_code && c.company_code === val) || (c.branch_code && c.branch_code === val)); if(match) selectedId = match.id;">
                                    <datalist id="company-list">
                                        @foreach ($companys as $company)
                                            <option value="{{ $company->company_name }}">
                                                {{ $company->company_code ?? $company->branch_code }}
                                            </option>
                                        @endforeach
                                    </datalist>

                                    {{-- Hidden input holds the actual foreign key for the 'users' table [3] --}}
                                    <input type="hidden" name="company_id" x-model="selectedId">
                                </div>
                                <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                                <p class="mt-3 text-[10px] text-blue-400 italic">
                                    {{ __('Architecture Note: Catalog visibility whitelists are derived from the selected Company.') }}
                                    [3, 4]
                                </p>
                            </div>

                            {{-- Responsible CS Assignment --}}
                            <div>
                                <x-input-label for="assigned_cs_id" :value="__('Designated Customer Service Representative')"
                                    class="text-[10px] uppercase font-black" />
                                <select name="assigned_cs_id" id="assigned_cs_id"
                                    class="mt-1 block w-full border-blue-200 rounded-xl shadow-sm text-sm">
                                    <option value="">{{ __('-- Auto-Assign / Unassigned --') }}</option>
                                    @foreach ($csStaffMembers as $cs)
                                        <option value="{{ $cs->id }}"
                                            {{ old('assigned_cs_id') == $cs->id || (!old('assigned_cs_id') && auth()->id() == $cs->id) ? 'selected' : '' }}>
                                            {{ $cs->name }} ({{ $cs->login_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: SECURITY [Passwords] -->
                    <div class="space-y-6 pt-6 border-t border-gray-100">
                        <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest">
                            {{ __('Step 3: Access Control') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <x-input-label for="password" :value="__('Initial Password')"
                                    class="text-[10px] uppercase font-black" />
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
                    </div>

                    <!-- SUBMIT ACTIONS -->
                    <div class="flex items-center justify-end pt-8 border-t border-gray-100 gap-4">
                        <a href="{{ auth()->user()->hasAnyRole(['admin', 'cs_leader'])? route('users.index'): route('users.assigned') }}"
                            class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button
                            class="bg-blue-600 hover:bg-blue-700 py-4 px-10 rounded-2xl shadow-lg shadow-blue-200 transition-all uppercase text-[10px] font-black">
                            {{ __('Finalize Account Creation') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
