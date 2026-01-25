<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('⚙️ Edit User: ') }} {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl sm:rounded-2xl border border-gray-100" x-data="{ role: '{{ old('role', $user->roles->first()->name ?? '') }}' }">

                {{-- PRIMARY UPDATE FORM [Section 3.a.1] --}}
                <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $user->name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="login_id" :value="__('Login ID (Locked)')" />
                            <x-text-input id="login_id" type="text"
                                class="mt-1 block w-full bg-gray-50 cursor-not-allowed" :value="$user->login_id" disabled />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email', $user->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Account Status')" />
                            <select id="status" name="status"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @if ($user->hasRole('admin')) bg-gray-100 cursor-not-allowed @endif"
                                @if ($user->hasRole('admin')) disabled @endif>
                                <option value="active"
                                    {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="deactive"
                                    {{ old('status', $user->status) === 'deactive' ? 'selected' : '' }}>Deactive
                                </option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>
                    </div>

                    {{-- Role Assignment (Read-Only per Section 2.a audit integrity [3]) --}}
                    <div>
                        <x-input-label for="role_display" :value="__('Assigned Role')" />
                        <select id="role_display"
                            class="block mt-1 w-full border-gray-300 bg-gray-100 cursor-not-allowed rounded-md shadow-sm"
                            disabled>
                            @foreach ($roles as $roleOption)
                                <option value="{{ $roleOption->name }}"
                                    @if ($user->hasRole($roleOption->name)) selected @endif>
                                    {{ ucfirst(str_replace('_', ' ', $roleOption->name)) }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="role" value="{{ $user->roles->first()->name ?? '' }}">
                    </div>

                    {{-- Company & Logistics: Only visible if Customer --}}
                    <div x-show="role === 'customer'" class="pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4">
                            {{ __('Company & Logistics') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="company_name" :value="__('Company Name')" />
                                <x-text-input id="company_name" name="company_name" type="text"
                                    class="mt-1 block w-full" :value="old('company_name', $user->details->company_name ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                            </div>
                            <div>
                                <x-input-label for="company_reg_no" :value="__('Company Reg No (SSM)')" />
                                <x-text-input id="company_reg_no" name="company_reg_no" type="text"
                                    class="mt-1 block w-full" :value="old('company_reg_no', $user->details->company_reg_no ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('company_reg_no')" />
                            </div>
                            <div>
                                <x-input-label for="pic_name" :value="__('PIC Name')" />
                                <x-text-input id="pic_name" name="pic_name" type="text" class="mt-1 block w-full"
                                    :value="old('pic_name', $user->details->pic_name ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('pic_name')" />
                            </div>
                            <div>
                                <x-input-label for="pic_phone" :value="__('PIC Phone')" />
                                <x-text-input id="pic_phone" name="pic_phone" type="text" class="mt-1 block w-full"
                                    :value="old('pic_phone', $user->details->pic_phone ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('pic_phone')" />
                            </div>
                        </div>

                        <div class="pt-6">
                            <x-input-label for="delivery_address" :value="__('Delivery Address')" />
                            <textarea id="delivery_address" name="delivery_address" rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">{{ old('delivery_address', $user->details->delivery_address ?? '') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('delivery_address')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
                        {{-- CS Staff Assignment --}}
                        @can('reassign_customers')
                            <div>
                                <x-input-label for="assigned_cs_id" :value="__('Assigned CS Staff')" />
                                <select id="assigned_cs_id" name="assigned_cs_id"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">-- No CS Assigned --</option>
                                    @foreach ($csStaffMembers as $staff)
                                        <option value="{{ $staff->id }}"
                                            {{ old('assigned_cs_id', $user->assigned_cs_id) == $staff->id ? 'selected' : '' }}>
                                            {{ $staff->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('assigned_cs_id')" />
                            </div>
                        @endcan

                        {{-- Catalog Selection [Section 3.a.1] --}}
                        <div x-show="role === 'customer'">
                            <x-input-label for="catalog_id" :value="__('Assigned Catalog')" />
                            <select id="catalog_id" name="catalog_id"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">{{ __('-- Inherit from Parent --') }}</option>
                                @foreach ($catalogs as $catalog)
                                    <option value="{{ $catalog->id }}"
                                        {{ old('catalog_id', $user->catalog_id) == $catalog->id ? 'selected' : '' }}>
                                        {{ $catalog->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('catalog_id')" />
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <x-input-label for="password" :value="__('New Password (Optional)')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                        <x-primary-button>{{ __('Update User Account') }}</x-primary-button>
                    </div>
                </form>

                {{-- Branch Management and Dangerous Actions sections follow separately... --}}
                {{-- BRANCH MANAGEMENT SECTION [Section 3.a.2] --}}
                @if ($user->hasRole('customer') && is_null($user->parent_id))
                    <div
                        class="mt-12 p-6 bg-gray-50 rounded-xl border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="text-gray-800 font-black uppercase text-sm">{{ __('Branch Management') }}</h3>
                            <p class="text-gray-600 text-[10px] mt-1 italic">
                                {{ __('Create a sub-account that inherits this HQ\'s settings.') }}</p>
                        </div>
                        <a href="{{ route('users.create', ['parent_id' => $user->id]) }}"
                            class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-black uppercase transition shadow-sm">
                            + {{ __('Add New Branch') }}
                        </a>
                    </div>
                @endif

                {{-- DANGEROUS ACTIONS SECTION [Section 3.c] --}}
                @can('edit_users')
                    <div class="mt-12 pt-8 border-t-2 border-dashed border-gray-100">
                        @if ($user->canBeDeleted())
                            <div
                                class="bg-red-50 p-6 rounded-2xl border border-red-100 flex flex-col md:flex-row justify-between items-center gap-6">
                                <div>
                                    <h3 class="text-red-800 font-black uppercase text-sm leading-none">
                                        {{ __('Dangerous: Remove Account') }}</h3>
                                    <p class="text-red-600 text-[10px] mt-2 italic">
                                        {{ is_null($user->parent_id)
                                            ? __('Deleting this HQ will also permanently remove all associated branches [Section 3.c.1].')
                                            : __('This will permanently remove this specific account.') }}
                                    </p>
                                </div>
                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                    onsubmit="return confirm('Are you absolutely sure? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-xl text-xs font-black uppercase transition shadow-lg">
                                        {{ __('Confirm Hard Delete') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest italic">
                                    {{ __('🔒 This account is locked from deletion because it is linked to historical order records [Section 3.c].') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
