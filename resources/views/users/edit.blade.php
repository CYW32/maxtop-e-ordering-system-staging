<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('users.update', $user->id) }}">
                    @csrf
                    @method('PUT') {{-- Required for Update --}}

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Full Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="$user->name" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="login_id" :value="__('Login ID')" />
                        <x-text-input id="login_id" class="block mt-1 w-full bg-gray-100" type="text"
                            :value="$user->login_id" disabled />
                        <span class="text-xs text-gray-500">Login ID cannot be changed.</span>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="$user->email" required />
                    </div>

                    <!-- ROLE SECTION -->
                    <div class="mb-4">
                        <x-input-label for="role" :value="__('User Role')" />
                        <input type="hidden" name="role" value="{{ $user->roles->first()->name }}">
                        <select id="role"
                            class="block mt-1 w-full border-gray-300 bg-gray-100 cursor-not-allowed rounded-md shadow-sm"
                            disabled>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @if ($user->hasRole($role->name)) selected @endif>
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-red-600 mt-1 uppercase font-bold">
                            {{ __('Role cannot be changed after creation for security integrity.') }}</p>
                        {{-- CRITICAL: Reason and Hidden Field for Admin Stability --}}
                        @if ($user->hasRole('admin'))
                            <p class="text-xs text-red-500 mt-1 italic">Reason: The System Admin role is permanent to
                                ensure developer-level access.</p>
                            <input type="hidden" name="role" value="admin">
                        @endif
                    </div>

                    <!-- STATUS SECTION -->
                    <div class="mb-4">
                        <x-input-label for="status" :value="__('Status')" />
                        <select name="status" id="status"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @if ($user->hasRole('admin')) bg-gray-100 cursor-not-allowed @endif"
                            @if ($user->hasRole('admin')) disabled @endif>
                            <option value="active" @if ($user->status === 'active') selected @endif>Active</option>
                            <option value="deactive" @if ($user->status === 'deactive') selected @endif>Deactive</option>
                        </select>
                        {{-- CRITICAL: Reason and Hidden Field for Admin Stability --}}
                        @if ($user->hasRole('admin'))
                            <p class="text-xs text-red-500 mt-1 italic">Reason: System Admin must remain active to
                                prevent management lockout.</p>
                            <input type="hidden" name="status" value="active">
                        @endif
                    </div>

                    @if ($user->hasRole('customer'))
                        <div class="mb-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Company & PIC Information') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="company_name" :value="__('Company Name')" />
                                    <x-text-input id="company_name" name="company_name" type="text"
                                        class="mt-1 block w-full" :value="old('company_name', $user->details->company_name ?? '')" />
                                </div>

                                <div>
                                    <x-input-label for="company_reg_no" :value="__('Company Reg No (SSM)')" />
                                    <x-text-input id="company_reg_no" name="company_reg_no" type="text"
                                        class="mt-1 block w-full" :value="old('company_reg_no', $user->details->company_reg_no ?? '')" />
                                </div>

                                <div>
                                    <x-input-label for="pic_name" :value="__('PIC Name')" />
                                    <x-text-input id="pic_name" name="pic_name" type="text"
                                        class="mt-1 block w-full" :value="old('pic_name', $user->details->pic_name ?? '')" />
                                </div>

                                <div>
                                    <x-input-label for="pic_phone" :value="__('PIC Phone')" />
                                    <x-text-input id="pic_phone" name="pic_phone" type="text"
                                        class="mt-1 block w-full" :value="old('pic_phone', $user->details->pic_phone ?? '')" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <x-input-label for="delivery_address" :value="__('Delivery Address')" />
                                <textarea id="delivery_address" name="delivery_address"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('delivery_address', $user->details->delivery_address ?? '') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <div>
                                    <x-input-label for="city" :value="__('City')" />
                                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                                        :value="old('city', $user->details->city ?? '')" />
                                </div>
                                <div>
                                    <x-input-label for="state" :value="__('State')" />
                                    <x-text-input id="state" name="state" type="text" class="mt-1 block w-full"
                                        :value="old('state', $user->details->state ?? '')" />
                                </div>
                                <div>
                                    <x-input-label for="postal_code" :value="__('Postal Code')" />
                                    <x-text-input id="postal_code" name="postal_code" type="text"
                                        class="mt-1 block w-full" :value="old('postal_code', $user->details->postal_code ?? '')" />
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- ASSIGNED CS REPRESENTATIVE SECTION -->
                    @can('reassign_customers')
                        <div class="mb-4">
                            <x-input-label for="assigned_cs_id" :value="__('Assigned CS Representative')" />
                            <select name="assigned_cs_id" id="assigned_cs_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @if ($user->hasRole('admin')) bg-gray-100 cursor-not-allowed @endif"
                                @if ($user->hasRole('admin')) disabled @endif>
                                <option value="">-- No CS Assigned --</option>
                                @foreach ($csStaffMembers as $staff)
                                    <option value="{{ $staff->id }}"
                                        {{ $user->assigned_cs_id == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }} ({{ $staff->roles->first()->name ?? 'No Role' }})
                                    </option>
                                @endforeach
                            </select>
                            {{-- CRITICAL: Reason for Admin --}}
                            @if ($user->hasRole('admin'))
                                <p class="text-xs text-red-500 mt-1 italic">Reason: Admins cannot be assigned to CS Staff
                                    as they oversee the entire system.</p>
                            @endif
                        </div>
                    @endcan

                    @if ($user->hasRole('customer'))
                        <div class="mt-4">
                            <x-input-label for="catalog_id" :value="__('Assigned Catalog')" />
                            <select name="catalog_id" id="catalog_id"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">{{ __('-- No Catalog (Will inherit from Parent) --') }}
                                </option>
                                @foreach ($catalogs as $catalog)
                                    <option value="{{ $catalog->id }}"
                                        {{ $user->catalog_id == $catalog->id ? 'selected' : '' }}>
                                        {{ $catalog->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-4">
                        <x-input-label for="password" :value="__('New Password (Optional)')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                            autocomplete="new-password" />
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep the current password.</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Update User') }}</x-primary-button>

                        <a href="{{ auth()->user()->hasAnyRole(['admin', 'cs_leader'])? route('users.index'): route('users.assigned') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('Cancel') }}
                        </a>

                        @if ($user->hasRole('customer') && is_null($user->parent_id))
                            <div class="mt-6 p-4 bg-gray-50 border rounded-lg flex justify-between items-center">
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900">{{ __('Branch Management') }}</h4>
                                    <p class="text-xs text-gray-500">
                                        {{ __('Create a sub-account that inherits this HQ\'s settings.') }}</p>
                                </div>
                                <a href="{{ route('users.create', ['parent_id' => $user->id]) }}"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-black uppercase transition">
                                    {{ __('+ Add New Branch') }}
                                </a>
                            </div>
                        @endif

                        {{-- Fulfills Section 3.c: Standardized Hard Delete for ALL eligible roles --}}
                        @can('edit_users')
                            @if ($user->canBeDeleted())
                                <div class="mt-12 pt-8 border-t border-red-100">
                                    <div
                                        class="bg-red-50 p-6 rounded-xl border border-red-100 flex flex-col md:flex-row justify-between items-center">
                                        <div>
                                            <h3 class="text-red-800 font-black uppercase text-sm">
                                                {{ __('Dangerous: Remove Account') }}</h3>
                                            <p class="text-red-600 text-xs mt-1">
                                                {{ is_null($user->parent_id)
                                                    ? __('Deleting this HQ will also permanently remove all its associated branches.')
                                                    : __('This will permanently remove this account.') }}
                                            </p>
                                        </div>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                            onsubmit="return confirm('Are you absolutely sure? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg text-xs font-black uppercase transition shadow-lg">
                                                {{ __('Confirm Hard Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                {{-- Fulfills Section 3.c.3: UI Warning for Locked Records --}}
                                <div class="mt-12 pt-8 border-t border-gray-100 italic text-gray-400 text-xs text-center">
                                    {{ __('This account is unable to hard delete because it is linked to historical order records.') }}
                                </div>
                            @endif
                        @endcan
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
