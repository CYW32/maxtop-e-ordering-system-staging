<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Edit Business Entity') }}: <span class="text-blue-600">{{ $company->company_name }}</span>
            </h2>
            <a href="{{ route('companys.index') }}"
                class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back To Business Directory') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Back Link --}}
            <a href="{{ route('companys.index') }}"
                class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back To Business Directory') }}
            </a>

            <form method="POST" action="{{ route('companys.update', $company) }}" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- IMMUTABLE IDENTITY SECTION (Only Codes Locked Now) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                    <div
                        class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        {{ __('System Identity (Locked)') }}
                    </div>

                    <div class="p-6 bg-gray-50/50 rounded-2xl border border-gray-100">
                        <div>
                            @if (is_null($company->parent_id))
                                <p class="text-[9px] uppercase font-black text-gray-400">{{ __('HQ Company Code') }}</p>
                                <div class="mt-1 font-mono text-xl font-black text-blue-600">
                                    {{ $company->company_code }}</div>
                            @else
                                <p class="text-[9px] uppercase font-black text-gray-400">
                                    {{ __('Branch Identification Code') }}</p>
                                <div class="mt-1 font-mono text-xl font-black text-blue-600">
                                    {{ $company->branch_code }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- EDITABLE BUSINESS DATA --}}
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10 space-y-8">
                    <div
                        class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        {{ __('Business Parameters & Settings') }}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Company Name is now editable --}}
                        <div class="md:col-span-2">
                            <x-input-label for="company_name" :value="__('Registered Business Name')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="company_name" name="company_name" type="text"
                                class="w-full font-bold text-gray-800 uppercase" :value="old('company_name', $company->company_name)" required />
                        </div>

                        <div>
                            <x-input-label for="company_reg_no" :value="__('SSM Registration No')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="company_reg_no" name="company_reg_no" type="text"
                                class="w-full font-bold text-gray-800" :value="old('company_reg_no', $company->company_reg_no)" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Operational Status')"
                                class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <select name="status" id="status"
                                class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm font-black uppercase">
                                <option value="active" @selected(old('status', $company->status ?? 'active') === 'active')>{{ __('Active (Operational)') }}
                                </option>
                                <option value="inactive" @selected(old('status', $company->status) === 'inactive')>{{ __('Inactive (Suspended)') }}
                                </option>
                            </select>
                        </div>

                        <div class="md:col-span-2 pt-4">
                            <x-input-label for="catalog_id" :value="__('Catalog Whitelist Assignment')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <select name="catalog_id" id="catalog_id"
                                class="w-full border-gray-300 rounded-xl text-sm font-bold text-gray-800 shadow-sm focus:ring-blue-500 focus:border-blue-500 cursor-pointer py-3">
                                <option value="">
                                    {{ $company->parent_id ? __('-- Inherit from Headquarters --') : __('-- No Catalog --') }}
                                </option>
                                @foreach ($catalogs as $catalog)
                                    <option value="{{ $catalog->id }}"
                                        {{ old('catalog_id', $company->catalog_id) == $catalog->id ? 'selected' : '' }}>
                                        {{ $catalog->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- LOGISTICS & PIC --}}
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10 space-y-8">
                    <div
                        class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        {{ __('Contact Person & Fulfillment Details') }}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="pic_name" :value="__('PIC Full Name')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="pic_name" name="pic_name" type="text"
                                class="w-full font-bold text-gray-800" :value="old('pic_name', $company->pic_name)" />
                        </div>
                        <div>
                            <x-input-label for="pic_phone" :value="__('PIC Contact Number')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="pic_phone" name="pic_phone" type="text"
                                class="w-full font-bold text-gray-800 font-mono" :value="old('pic_phone', $company->pic_phone)" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="delivery_address" :value="__('Fulfillment Delivery Address')"
                            class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                        <textarea id="delivery_address" name="delivery_address" rows="4"
                            class="w-full border-gray-300 rounded-xl text-sm font-bold text-gray-800 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            required>{{ old('delivery_address', $company->delivery_address) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="city" :value="__('City')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="city" name="city" type="text"
                                class="w-full font-bold text-gray-800" :value="old('city', $company->city)" />
                        </div>
                        <div>
                            <x-input-label for="state" :value="__('State')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="state" name="state" type="text"
                                class="w-full font-bold text-gray-800" :value="old('state', $company->state)" />
                        </div>
                        <div>
                            <x-input-label for="postal_code" :value="__('Postcode')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="postal_code" name="postal_code" type="text"
                                class="w-full font-bold text-gray-800 font-mono" :value="old('postal_code', $company->postal_code)" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-6 pt-4 pb-12">
                    <a href="{{ route('companys.index') }}"
                        class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 tracking-widest transition">{{ __('Cancel') }}</a>
                    <x-primary-button
                        class="bg-gray-900 hover:bg-black py-4 px-12 rounded-2xl shadow-lg transition-all uppercase text-[10px] font-black tracking-widest">
                        {{ __('Save & Update Entity') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
