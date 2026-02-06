<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                    {{ __('Manage Business Entity') }}
                </h2>
                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mt-1">
                    {{ $company->company_name }}
                    <span class="text-gray-400">({{ $company->company_code ?? $company->branch_code }})</span>
                </p>
            </div>

            {{-- Fulfills Addendum 3.c: Contextual Branch Creation for HQs --}}
            @if (is_null($company->parent_id))
                <a href="{{ route('companys.create', ['parent_id' => $company->id]) }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-2xl text-xs font-black uppercase transition-all shadow-lg shadow-green-100">
                    {{ __('+ Add New Branch') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- ARCHITECTURE: Server-Aware Alpine State for Mutex Logic [Addendum 1.d] --}}
            <div x-data="{
                isBranch: {{ $company->parent_id ? 'true' : 'false' }},
                parentId: '{{ old('parent_id', $company->parent_id) }}'
            }"
                class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">

                <form method="POST" action="{{ route('companys.update', $company) }}" class="space-y-10">
                    @csrf
                    @method('PUT')

                    <!-- SECTION 1: BUSINESS HIERARCHY [Addendum 3.c] -->
                    <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100">
                        <h3
                            class="text-[10px] font-black uppercase text-blue-600 mb-4 tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ __('Hierarchy Configuration') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <x-input-label :value="__('Entity Relationship')"
                                    class="text-[10px] uppercase font-black text-gray-500 mb-2" />
                                <div class="flex gap-6">
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="radio" x-model="isBranch" :value="false"
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span
                                            class="ml-2 text-xs font-black text-gray-700 uppercase group-hover:text-blue-600 transition">{{ __('Main HQ') }}</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="radio" x-model="isBranch" :value="true"
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span
                                            class="ml-2 text-xs font-black text-gray-700 uppercase group-hover:text-blue-600 transition">{{ __('Branch Office') }}</span>
                                    </label>
                                </div>
                            </div>

                            <div x-show="isBranch" x-transition.duration.300ms>
                                <x-input-label for="parent_id" :value="__('Parent Headquarters')"
                                    class="text-[10px] uppercase font-black text-gray-500" />
                                <select id="parent_id" name="parent_id" x-model="parentId"
                                    class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm">
                                    <option value="">{{ __('-- Select Parent HQ --') }}</option>
                                    @foreach ($hqs as $hq)
                                        <option value="{{ $hq->id }}">{{ $hq->company_name }}
                                            ({{ $hq->company_code }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: IDENTIFICATION & BRANDING [Addendum 1.d] -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div x-show="!isBranch">
                            <x-input-label for="company_code" :value="__('Company Code (HQ)')"
                                class="text-[10px] uppercase font-black" />
                            <x-text-input id="company_code" name="company_code" type="text"
                                class="mt-1 block w-full uppercase font-mono bg-gray-50" :value="old('company_code', $company->company_code)" />
                            <x-input-error :messages="$errors->get('company_code')" class="mt-2" />
                        </div>

                        <div x-show="isBranch">
                            <x-input-label for="branch_code" :value="__('Branch Code')"
                                class="text-[10px] uppercase font-black" />
                            <x-text-input id="branch_code" name="branch_code" type="text"
                                class="mt-1 block w-full uppercase font-mono" :value="old('branch_code', $company->branch_code)" />
                            <x-input-error :messages="$errors->get('branch_code')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="company_name" :value="__('Registered Business Name')"
                                class="text-[10px] uppercase font-black" />
                            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full"
                                :value="old('company_name', $company->company_name)" required />
                            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                        </div>
                    </div>

                    <!-- SECTION 3: LOGISTICS & PIC [Preserved Attributes] -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-gray-100">
                        <div class="space-y-6">
                            <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest">
                                {{ __('Logistics & Delivery') }}</h3>

                            <div>
                                <x-input-label for="delivery_address" :value="__('Primary Delivery Address')"
                                    class="text-[10px] uppercase font-black" />
                                <textarea id="delivery_address" name="delivery_address" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm text-sm focus:ring-blue-500" required>{{ old('delivery_address', $company->delivery_address) }}</textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="city" :value="__('City')"
                                        class="text-[10px] uppercase font-black" />
                                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                                        :value="old('city', $company->city)" />
                                </div>
                                <div>
                                    <x-input-label for="postal_code" :value="__('Postal Code')"
                                        class="text-[10px] uppercase font-black" />
                                    <x-text-input id="postal_code" name="postal_code" type="text"
                                        class="mt-1 block w-full" :value="old('postal_code', $company->postal_code)" />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest">
                                {{ __('Contact Information') }}</h3>

                            <div>
                                <x-input-label for="company_reg_no" :value="__('Company Reg No (SSM)')"
                                    class="text-[10px] uppercase font-black" />
                                <x-text-input id="company_reg_no" name="company_reg_no" type="text"
                                    class="mt-1 block w-full" :value="old('company_reg_no', $company->company_reg_no)" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="pic_name" :value="__('PIC Name')"
                                        class="text-[10px] uppercase font-black" />
                                    <x-text-input id="pic_name" name="pic_name" type="text"
                                        class="mt-1 block w-full" :value="old('pic_name', $company->pic_name)" />
                                </div>
                                <div>
                                    <x-input-label for="pic_phone" :value="__('PIC Phone')"
                                        class="text-[10px] uppercase font-black" />
                                    <x-text-input id="pic_phone" name="pic_phone" type="text"
                                        class="mt-1 block w-full" :value="old('pic_phone', $company->pic_phone)" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: CATALOG ASSIGNMENT [Addendum 1.b & Backbone 3.a.2] -->
                    <div class="p-6 bg-gray-900 rounded-2xl text-white shadow-xl">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-[10px] font-black uppercase text-blue-400 tracking-widest">
                                {{ __('Ordering Configuration') }}</h3>
                            <span
                                class="px-2 py-1 bg-blue-500/20 text-blue-300 text-[8px] font-black uppercase rounded border border-blue-500/30">{{ __('Single Catalog Policy') }}</span>
                        </div>

                        <x-input-label for="catalog_id" :value="__('Assigned Item Whitelist')"
                            class="text-gray-400 text-[10px] font-black uppercase" />
                        <select id="catalog_id" name="catalog_id"
                            class="mt-2 block w-full bg-gray-800 border-gray-700 text-white rounded-xl shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">
                                {{ $company->parent_id ? __('-- Inherit from Headquarters --') : __('-- No Catalog Assigned --') }}
                            </option>
                            @foreach ($catalogs as $catalog)
                                <option value="{{ $catalog->id }}"
                                    {{ old('catalog_id', $company->catalog_id) == $catalog->id ? 'selected' : '' }}>
                                    {{ $catalog->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-3 text-[10px] text-gray-500 italic">
                            {{ __('Visibility Rationale: Changing the catalog here updates the item whitelist for all linked users immediately.') }}
                            [1]
                        </p>
                    </div>

                    <div class="flex items-center justify-end pt-6 border-t border-gray-100 gap-4">
                        <a href="{{ route('companys.index') }}"
                            class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">{{ __('Cancel') }}</a>
                        <x-primary-button
                            class="bg-blue-600 hover:bg-blue-700 py-3 px-10 rounded-2xl shadow-lg shadow-blue-100 transition-all uppercase text-[10px] font-black">
                            {{ __('Update Business Entity') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
