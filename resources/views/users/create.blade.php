<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ $parent ? __('Add Branch for ') . $parent->name : __('Create New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl sm:rounded-2xl border border-gray-100" x-data="{ role: '{{ old('role', $parent ? 'customer' : '') }}' }">
                {{-- Fulfills dynamic visibility request --}}

                <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
                    @csrf

                    @php
                        $isRestricted = auth()->user()->hasRole('cs_staff') || $parent;
                    @endphp

                    @if ($parent)
                        <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                    @endif

                    @if ($isRestricted)
                        <input type="hidden" name="role" value="customer">
                    @endif

                    <div class="border-b border-gray-100 pb-4">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">
                            {{ __('Account Credentials') }}</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Role Selection --}}
                        <div class="md:col-span-2">
                            <x-input-label for="role" :value="__('Select User Role')" />
                            <select id="role" name="role" x-model="role" {{-- Alpine Binding --}}
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm @if ($isRestricted) bg-gray-50 cursor-not-allowed @endif"
                                @if ($isRestricted) disabled @endif>
                                <option value="">{{ __('Select a Role') }}</option>
                                @foreach ($roles as $roleOption)
                                    <option value="{{ $roleOption->name }}">
                                        {{ ucfirst(str_replace('_', ' ', $roleOption->name)) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('role')" />
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="login_id" :value="__('Login ID (Unique)')" />
                            <x-text-input id="login_id" name="login_id" type="text"
                                class="mt-1 block w-full uppercase" :value="old('login_id')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('login_id')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                required />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                        </div>
                    </div>

                    {{-- Customer Specific Information: Section 3.a Hierarchy --}}
                    <div x-show="role === 'customer'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="pt-6 border-t border-gray-100">

                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4">
                            {{ __('Company & Logistics') }}</h3>

                        @if ($parent)
                            <div class="bg-blue-50 p-3 rounded border border-blue-100 text-[10px] text-blue-700 mb-4">
                                *{{ __('INHERITANCE:') }}*
                                {{ __('This branch will automatically inherit settings from ') }} {{ $parent->name }}
                                [6].
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="company_name" :value="__('Company Name')" />
                                <x-text-input id="company_name" name="company_name" type="text"
                                    class="mt-1 block w-full" :value="old('company_name', $parent->details->company_name ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                            </div>
                            <div>
                                <x-input-label for="company_reg_no" :value="__('Company Reg No (SSM)')" />
                                <x-text-input id="company_reg_no" name="company_reg_no" type="text"
                                    class="mt-1 block w-full" :value="old('company_reg_no', $parent->details->company_reg_no ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('company_reg_no')" />
                            </div>
                            <div>
                                <x-input-label for="pic_name" :value="__('PIC Name')" />
                                <x-text-input id="pic_name" name="pic_name" type="text" class="mt-1 block w-full"
                                    :value="old('pic_name', $parent->details->pic_name ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('pic_name')" />
                            </div>
                            <div>
                                <x-input-label for="pic_phone" :value="__('PIC Phone')" />
                                <x-text-input id="pic_phone" name="pic_phone" type="text" class="mt-1 block w-full"
                                    :value="old('pic_phone', $parent->details->pic_phone ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('pic_phone')" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label for="delivery_address" :value="__('Delivery Address')" />
                            <textarea id="delivery_address" name="delivery_address" rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">{{ old('delivery_address', $parent->details->delivery_address ?? '') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('delivery_address')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <x-input-label for="city" :value="__('City')" />
                                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                                    :value="old('city', $parent->details->city ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('city')" />
                            </div>
                            <div>
                                <x-input-label for="state" :value="__('State')" />
                                <x-text-input id="state" name="state" type="text" class="mt-1 block w-full"
                                    :value="old('state', $parent->details->state ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('state')" />
                            </div>
                            <div>
                                <x-input-label for="postal_code" :value="__('Postal Code')" />
                                <x-text-input id="postal_code" name="postal_code" type="text"
                                    class="mt-1 block w-full" :value="old('postal_code', $parent->details->postal_code ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                        <a href="{{ auth()->user()->hasAnyRole(['admin', 'cs_leader'])? route('users.index'): route('users.assigned') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button class="ml-4">
                            {{ __('Create User Account') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
