<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('Manage Business Entity') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('companys.update', $company) }}"
                class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8 space-y-10">
                @csrf
                @method('PUT')

                {{-- IMMUTABLE IDENTITY SECTION [Addendum 1.d, 3.c] --}}
                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                    <label
                        class="text-[10px] uppercase font-black text-gray-500 mb-4 block underline decoration-gray-200 underline-offset-4">{{ __('System Identity (Locked)') }}</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <x-input-label :value="__('Registered Name')" class="text-[9px] uppercase font-black text-gray-400" />
                            <div class="mt-1 text-sm font-black text-gray-800 uppercase">{{ $company->company_name }}
                            </div>
                            <p class="mt-2 text-[8px] text-blue-400 italic uppercase">
                                {{ __('Contact Admin to change legal identity.') }}</p>
                        </div>
                        <div>
                            @if (is_null($company->parent_id))
                                <x-input-label :value="__('HQ Company Code')"
                                    class="text-[9px] uppercase font-black text-gray-400" />
                                <div class="mt-1 font-mono text-sm font-black text-blue-600">
                                    {{ $company->company_code }}</div>
                            @else
                                <x-input-label :value="__('Branch Identification Code')"
                                    class="text-[9px] uppercase font-black text-gray-400" />
                                <div class="mt-1 font-mono text-sm font-black text-blue-600">{{ $company->branch_code }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- EDITABLE BUSINESS DATA --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="company_reg_no" :value="__('SSM Registration No')"
                            class="text-[10px] uppercase font-black" />
                        <x-text-input id="company_reg_no" name="company_reg_no" type="text" class="mt-1 block w-full"
                            :value="old('company_reg_no', $company->company_reg_no)" />
                    </div>
                    <div>
                        <x-input-label for="catalog_id" :value="__('Catalog Whitelist Assignment')" class="text-[10px] uppercase font-black" />
                        <select name="catalog_id" id="catalog_id"
                            class="mt-1 block w-full border-gray-300 rounded-xl text-sm focus:ring-blue-500">
                            <option value="">
                                {{ $company->parent_id ? __('-- Inherit from Headquarters --') : __('-- No Catalog --') }}
                            </option>
                            @foreach ($catalogs as $catalog)
                                <option value="{{ $catalog->id }}"
                                    {{ old('catalog_id', $company->catalog_id) == $catalog->id ? 'selected' : '' }}>
                                    {{ $catalog->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- LOGISTICS & PIC --}}
                <div class="space-y-6 pt-6 border-t border-gray-50">
                    <div class="text-[10px] uppercase font-black text-gray-400 tracking-widest">
                        {{ __('Contact person & Fulfillment') }}</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="pic_name" :value="__('PIC Full Name')" class="text-[10px] uppercase font-black" />
                            <x-text-input id="pic_name" name="pic_name" type="text" class="mt-1 block w-full"
                                :value="old('pic_name', $company->pic_name)" />
                        </div>
                        <div>
                            <x-input-label for="pic_phone" :value="__('PIC Contact Number')" class="text-[10px] uppercase font-black" />
                            <x-text-input id="pic_phone" name="pic_phone" type="text" class="mt-1 block w-full"
                                :value="old('pic_phone', $company->pic_phone)" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="delivery_address" :value="__('Fulfillment Delivery Address')"
                            class="text-[10px] uppercase font-black" />
                        <textarea id="delivery_address" name="delivery_address"
                            class="mt-1 block w-full border-gray-300 rounded-xl text-sm focus:ring-blue-500" required>{{ old('delivery_address', $company->delivery_address) }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="postal_code" :value="__('Postcode')"
                                class="text-[10px] uppercase font-black" />
                            <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full"
                                :value="old('postal_code', $company->postal_code)" />
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="city" :value="__('City')" class="text-[10px] uppercase font-black" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                                :value="old('city', $company->city)" />
                        </div>
                        <div>
                            <x-input-label for="state" :value="__('State')" class="text-[10px] uppercase font-black" />
                            <x-text-input id="state" name="state" type="text" class="mt-1 block w-full"
                                :value="old('state', $company->state)" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <a href="{{ route('companys.index') }}"
                        class="text-xs font-black uppercase text-gray-400 py-3">{{ __('Cancel') }}</a>
                    <x-primary-button
                        class="bg-blue-600 hover:bg-blue-700 py-3 px-12 rounded-2xl text-[10px] font-black uppercase">
                        {{ __('Update Business Entity') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
