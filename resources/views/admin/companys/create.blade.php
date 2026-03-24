<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ $preselectedParent ? __('Add Branch to ') . $preselectedParent->company_name : __('Register New Business') }}
            </h2>
            <a href="{{ route('companys.index') }}"
                class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back To Business Directory') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        type: '{{ $preselectedParent ? 'branch' : old('entity_type', 'hq') }}',
        parentId: '{{ $preselectedParent->id ?? old('parent_id') }}'
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Back Link --}}
            <a href="{{ route('companys.index') }}"
                class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back To Business Directory') }}
            </a>

            <form method="POST" action="{{ route('companys.store') }}" class="space-y-8">
                @csrf

                {{-- VALIDATION ERROR ALERT (NEW) --}}
                @if ($errors->any())
                    <div class="p-5 bg-red-50 border border-red-200 rounded-[2rem] shadow-sm">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span
                                class="text-[11px] font-black uppercase text-red-800 tracking-wider">{{ __('Please fix the following errors:') }}</span>
                        </div>
                        <ul class="list-disc list-inside text-xs font-bold text-red-600 space-y-1 ml-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- 1. HIERARCHY & IDENTITY CARD --}}
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10 space-y-8">
                    <div
                        class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        {{ __('Entity Relationship & Identity') }}
                    </div>

                    {{-- HQ vs Branch Selector --}}
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray-400 mb-3">
                            {{ __('Business Type Selection') }}</p>
                        <div class="flex flex-col sm:flex-row items-center gap-4 mb-6">
                            <label
                                class="w-full flex-1 relative flex items-center p-4 border-2 rounded-2xl cursor-pointer transition-all"
                                :class="type === 'hq' ? 'bg-blue-50 border-blue-500' :
                                    'bg-gray-50 border-gray-100 hover:border-gray-200'">
                                <input type="radio" name="entity_type" value="hq" x-model="type"
                                    class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    @if ($preselectedParent) disabled @endif>
                                <div class="ml-3">
                                    <span class="block text-xs font-black text-gray-900 uppercase tracking-tight"
                                        :class="type === 'hq' ? 'text-blue-700' : ''">{{ __('Main HQ Company') }}</span>
                                    <span
                                        class="block text-[10px] font-bold text-gray-400 mt-0.5">{{ __('Independent parent entity') }}</span>
                                </div>
                            </label>

                            <label
                                class="w-full flex-1 relative flex items-center p-4 border-2 rounded-2xl cursor-pointer transition-all"
                                :class="type === 'branch' ? 'bg-blue-50 border-blue-500' :
                                    'bg-gray-50 border-gray-100 hover:border-gray-200'">
                                <input type="radio" name="entity_type" value="branch" x-model="type"
                                    class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="block text-xs font-black text-gray-900 uppercase tracking-tight"
                                        :class="type === 'branch' ? 'text-blue-700' : ''">{{ __('Branch Office') }}</span>
                                    <span
                                        class="block text-[10px] font-bold text-gray-400 mt-0.5">{{ __('Operates under an HQ') }}</span>
                                </div>
                            </label>
                        </div>

                        {{-- Parent HQ Dropdown (Visible only if Branch) --}}
                        <div x-show="type === 'branch'" x-transition
                            class="p-5 bg-gray-50 border border-gray-100 rounded-2xl mb-8">
                            <x-input-label for="parent_id" :value="__('Parent Headquarters Assignment')"
                                class="text-[10px] font-black uppercase text-blue-600 mb-2" />
                            <select name="parent_id" id="parent_id" x-model="parentId" :required="type === 'branch'"
                                class="w-full border-gray-200 rounded-xl shadow-sm text-sm font-bold text-gray-800 py-3 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                <option value="">{{ __('-- Choose Parent HQ --') }}</option>
                                @foreach ($hqs as $hq)
                                    <option value="{{ $hq->id }}">{{ $hq->company_name }}
                                        ({{ $hq->company_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Identity Inputs --}}
                    <div class="space-y-6 pt-6 border-t border-gray-50">
                        <div>
                            <x-input-label for="company_name" :value="__('Registered Business Name')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="company_name" name="company_name" type="text"
                                class="w-full font-bold text-gray-800" :value="old('company_name')" required />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div x-show="type === 'hq'" x-transition>
                                <x-input-label for="company_code" :value="__('HQ Company Code')"
                                    class="text-[10px] uppercase font-black text-blue-600 mb-2" />
                                <x-text-input id="company_code" name="company_code" type="text"
                                    class="w-full font-mono font-bold text-blue-700 uppercase" placeholder="HQ-XXXX"
                                    :value="old('company_code')" x-bind:required="type === 'hq'" />
                            </div>
                            <div x-show="type === 'branch'" x-transition style="display: none;">
                                <x-input-label for="branch_code" :value="__('Branch Identification Code')"
                                    class="text-[10px] uppercase font-black text-blue-600 mb-2" />
                                <x-text-input id="branch_code" name="branch_code" type="text"
                                    class="w-full font-mono font-bold text-blue-700 uppercase" placeholder="BR-XXXX"
                                    :value="old('branch_code')" x-bind:required="type === 'branch'" />
                            </div>
                            <div>
                                <x-input-label for="company_reg_no" :value="__('SSM Registration No')"
                                    class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                                <x-text-input id="company_reg_no" name="company_reg_no" type="text"
                                    class="w-full font-bold text-gray-800 uppercase" :value="old('company_reg_no')" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="catalog_id" :value="__('Assign Product Whitelist Catalog')"
                                    class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                <select name="catalog_id" id="catalog_id"
                                    class="w-full border-gray-300 rounded-xl text-sm font-bold text-gray-800 shadow-sm focus:ring-blue-500 focus:border-blue-500 cursor-pointer py-3">
                                    <option value="">{{ __('Inherit Parent Catalog (If Branch) / None') }}
                                    </option>
                                    @foreach ($catalogs as $catalog)
                                        <option value="{{ $catalog->id }}"
                                            {{ old('catalog_id') == $catalog->id ? 'selected' : '' }}>
                                            {{ $catalog->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. CONTACT & LOGISTICS CARD --}}
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
                        {{ __('Logistics & Contact Person') }}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="pic_name" :value="__('PIC Full Name')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="pic_name" name="pic_name" type="text"
                                class="w-full font-bold text-gray-800" :value="old('pic_name')" />
                        </div>
                        <div>
                            <x-input-label for="pic_phone" :value="__('PIC Phone Number')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="pic_phone" name="pic_phone" type="text"
                                class="w-full font-mono font-bold text-gray-800" :value="old('pic_phone')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="delivery_address" :value="__('Default Delivery Address')"
                            class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                        {{-- Address text area expanded to rows="4" --}}
                        <textarea id="delivery_address" name="delivery_address" rows="4"
                            class="w-full border-gray-300 rounded-xl text-sm font-bold text-gray-800 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            required>{{ old('delivery_address') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="city" :value="__('City')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="city" name="city" type="text"
                                class="w-full font-bold text-gray-800" :value="old('city')" />
                        </div>
                        <div>
                            <x-input-label for="state" :value="__('State')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="state" name="state" type="text"
                                class="w-full font-bold text-gray-800" :value="old('state')" />
                        </div>
                        <div class="col-span-1">
                            <x-input-label for="postal_code" :value="__('Postcode')"
                                class="text-[10px] uppercase font-black text-gray-400 mb-2" />
                            <x-text-input id="postal_code" name="postal_code" type="text"
                                class="w-full font-bold text-gray-800 font-mono" :value="old('postal_code')" />
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-6 pt-4 pb-12">
                    <a href="{{ route('companys.index') }}"
                        class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 tracking-widest transition">{{ __('Cancel') }}</a>
                    <x-primary-button
                        class="bg-blue-600 hover:bg-blue-700 py-4 px-12 rounded-2xl shadow-lg shadow-blue-200 transition-all uppercase text-[10px] font-black tracking-widest">
                        {{ __('Finalize Registration') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
