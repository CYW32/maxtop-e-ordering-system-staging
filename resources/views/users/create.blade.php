<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $parent ? __('Add Branch for ') . $parent->name : __('Create New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Logic: If CS Staff is creating or if adding a branch, force 'customer' role --}}
            @php
                $isRestricted = auth()->user()->hasRole('cs_staff') || $parent;
                $fixedRole = $isRestricted ? 'customer' : old('role', '');
            @endphp

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ role: '{{ $fixedRole }}' }">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    {{-- Fulfills Request: Link to HQ and inherit settings [3] --}}
                    @if ($parent)
                        <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                        <input type="hidden" name="catalog_id" value="{{ $parent->catalog_id }}">
                        <input type="hidden" name="assigned_cs_id" value="{{ $parent->assigned_cs_id }}">
                    @endif

                    {{-- If role is forced, pass it via hidden input since disabled selects don't submit data --}}
                    @if ($isRestricted)
                        <input type="hidden" name="role" value="customer">
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="font-bold text-gray-700 border-b pb-2">{{ __('Account Credentials') }}</h3>

                            <div>
                                <x-input-label for="role" :value="__('User Role')" />
                                <select id="role" name="role" x-model="role"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm @if ($isRestricted) bg-gray-100 cursor-not-allowed @endif"
                                    @if ($isRestricted) disabled @endif>
                                    <option value="">{{ __('Select a Role') }}</option>
                                    @foreach ($roles as $roleOption)
                                        <option value="{{ $roleOption->name }}"
                                            @if ($fixedRole == $roleOption->name) selected @endif>
                                            {{ ucfirst(str_replace('_', ' ', $roleOption->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($isRestricted)
                                    <p class="text-[10px] text-blue-600 mt-1 uppercase font-bold">
                                        {{ __('Fixed Context: Customer Role required') }}</p>
                                @endif
                            </div>

                            {{-- ... Name, Login ID, Email, Password inputs (same as current [4, 5]) ... --}}
                            <div>
                                <x-input-label for="name" :value="__('Full Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="old('name')" required />
                            </div>

                            <div>
                                <x-input-label for="login_id" :value="__('Login ID')" />
                                <x-text-input id="login_id" class="block mt-1 w-full" type="text" name="login_id"
                                    :value="old('login_id')" required />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email Address')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                    :value="old('email')" required />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                    required />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                    name="password_confirmation" required />
                            </div>
                        </div>

                        <div class="space-y-4" x-show="role === 'customer'" x-transition>
                            <h3 class="font-bold text-blue-700 border-b pb-2">{{ __('Company & Logistics') }}</h3>

                            @if ($parent)
                                <div
                                    class="bg-blue-50 p-3 rounded border border-blue-100 text-[10px] text-blue-700 mb-4">
                                    <strong>{{ __('INHERITANCE:') }}</strong>
                                    {{ __('This branch will automatically inherit Catalog and CS Staff from ') }}
                                    {{ $parent->name }}.
                                </div>
                            @endif

                            <div>
                                <x-input-label for="company_name" :value="__('Company/Branch Name')" />
                                <x-text-input id="company_name" class="block mt-1 w-full" type="text"
                                    name="company_name" :value="old('company_name', $parent->details->company_name ?? '')" />
                            </div>

                            <div>
                                <x-input-label for="company_reg_no" :value="__('Company Reg No (SSM)')" />
                                <x-text-input id="company_reg_no" class="block mt-1 w-full" type="text"
                                    name="company_reg_no" :value="old('company_reg_no', $parent->details->company_reg_no ?? '')" />
                            </div>

                            <div>
                                <x-input-label for="pic_name" :value="__('PIC Name')" />
                                <x-text-input id="pic_name" class="block mt-1 w-full" type="text" name="pic_name"
                                    :value="old('pic_name', $parent->details->pic_name ?? '')" />
                            </div>

                            <div>
                                <x-input-label for="pic_phone" :value="__('PIC Phone')" />
                                <x-text-input id="pic_phone" class="block mt-1 w-full" type="text" name="pic_phone"
                                    :value="old('pic_phone', $parent->details->pic_phone ?? '')" />
                            </div>

                            <div>
                                <x-input-label for="delivery_address" :value="__('Delivery Address')" />
                                <textarea id="delivery_address" class="block mt-1 w-full" type="text" name="delivery_address"
                                    :value="old('delivery_address', $parent->details->delivery_address ?? '')"></textarea>
                            </div>

                            <div>
                                <x-input-label for="city" :value="__('City')" />
                                <x-text-input id="city" class="block mt-1 w-full" type="text" name="city"
                                    :value="old('city', $parent->details->city ?? '')" />
                            </div>

                            <div>
                                <x-input-label for="state" :value="__('State')" />
                                <x-text-input id="state" class="block mt-1 w-full" type="text" name="state"
                                    :value="old('state', $parent->details->state ?? '')" />
                            </div>

                            <div>
                                <x-input-label for="postal_code" :value="__('Postal Code')" />
                                <x-text-input id="postal_code" class="block mt-1 w-full" type="text"
                                    name="postal_code" :value="old('postal_code', $parent->details->postal_code ?? '')" />
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-8 border-t pt-6">
                        <a href="{{ auth()->user()->hasAnyRole(['admin', 'cs_leader'])? route('users.index'): route('users.assigned') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('Cancel') }}
                        </a>

                        <x-primary-button>{{ __('Create User') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
