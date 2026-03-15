<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ $preselectedParent ? __('Add Branch to ') . $preselectedParent->company_name : __('Register New Business') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        type: '{{ $preselectedParent ? 'branch' : old('entity_type', 'hq') }}',
        parentId: '{{ $preselectedParent->id ?? old('parent_id') }}'
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('companys.store') }}"
                class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8 space-y-10">
                @csrf

                {{-- HIERARCHY SETUP --}}
                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                    <label
                        class="text-[10px] uppercase font-black text-gray-400 mb-4 block tracking-widest">{{ __('Entity Relationship') }}</label>
                    <div class="flex items-center gap-8 mb-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="entity_type" value="hq" x-model="type"
                                class="w-4 h-4 text-blue-600 border-gray-300"
                                @if ($preselectedParent) disabled @endif>
                            <span
                                class="ml-2 text-xs font-black text-gray-700 uppercase">{{ __('Main HQ Company') }}</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="entity_type" value="branch" x-model="type"
                                class="w-4 h-4 text-blue-600 border-gray-300">
                            <span
                                class="ml-2 text-xs font-black text-gray-700 uppercase">{{ __('Branch Office') }}</span>
                        </label>
                    </div>

                    <div x-show="type === 'branch'" x-transition class="space-y-2">
                        <x-input-label for="parent_id" :value="__('Parent Headquarters')"
                            class="text-[10px] font-black uppercase text-blue-600" />
                        <select name="parent_id" id="parent_id" x-model="parentId" :required="type === 'branch'"
                            class="w-full border-gray-300 rounded-xl shadow-sm text-sm">
                            <option value="">{{ __('-- Choose Parent HQ --') }}</option>
                            @foreach ($hqs as $hq)
                                <option value="{{ $hq->id }}">{{ $hq->company_name }} ({{ $hq->company_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- IDENTITY & CODES --}}
                <div class="space-y-6">
                    <div
                        class="text-[10px] uppercase font-black text-gray-400 tracking-widest border-b border-gray-50 pb-2">
                        {{ __('Business Identity') }}</div>
                    <div>
                        <x-input-label for="company_name" :value="__('Registered Business Name')" class="text-[10px] uppercase font-black" />
                        <x-text-input id="company_name" name="company_name" type="text"
                            class="mt-1 block w-full font-bold" :value="old('company_name')" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div x-show="type === 'hq'" x-transition>
                            <x-input-label for="company_code" :value="__('HQ Company Code')"
                                class="text-[10px] uppercase font-black text-blue-600" />
                            <x-text-input id="company_code" name="company_code" type="text"
                                class="mt-1 block w-full font-mono uppercase" placeholder="MA-XXXX" :value="old('company_code')"
                                x-bind:required="type === 'hq'" />
                        </div>
                        <div x-show="type === 'branch'" x-transition>
                            <x-input-label for="branch_code" :value="__('Branch Identification Code')"
                                class="text-[10px] uppercase font-black text-blue-600" />
                            <x-text-input id="branch_code" name="branch_code" type="text"
                                class="mt-1 block w-full font-mono uppercase" placeholder="BR-XXXX" :value="old('branch_code')"
                                x-bind:required="type === 'branch'" />
                        </div>
                        <div>
                            <x-input-label for="company_reg_no" :value="__('SSM Registration No')"
                                class="text-[10px] uppercase font-black" />
                            <x-text-input id="company_reg_no" name="company_reg_no" type="text"
                                class="mt-1 block w-full" :value="old('company_reg_no')" />
                        </div>
                    </div>
                </div>

                {{-- LOGISTICS & PIC --}}
                <div class="space-y-6">
                    <div
                        class="text-[10px] uppercase font-black text-gray-400 tracking-widest border-b border-gray-50 pb-2">
                        {{ __('Logistics & Contact Person') }}</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="pic_name" :value="__('PIC Full Name')" class="text-[10px] uppercase font-black" />
                            <x-text-input id="pic_name" name="pic_name" type="text" class="mt-1 block w-full"
                                :value="old('pic_name')" />
                        </div>
                        <div>
                            <x-input-label for="pic_phone" :value="__('PIC Phone Number')"
                                class="text-[10px] uppercase font-black" />
                            <x-text-input id="pic_phone" name="pic_phone" type="text" class="mt-1 block w-full"
                                :value="old('pic_phone')" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="delivery_address" :value="__('Default Delivery Address')"
                            class="text-[10px] uppercase font-black" />
                        <textarea id="delivery_address" name="delivery_address"
                            class="mt-1 block w-full border-gray-300 rounded-xl text-sm focus:ring-blue-500" required>{{ old('delivery_address') }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="col-span-1">
                            <x-input-label for="postal_code" :value="__('Postcode')"
                                class="text-[10px] uppercase font-black" />
                            <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full"
                                :value="old('postal_code')" />
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <x-input-label for="city" :value="__('City')" class="text-[10px] uppercase font-black" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                                :value="old('city')" />
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <x-input-label for="state" :value="__('State')"
                                class="text-[10px] uppercase font-black" />
                            <x-text-input id="state" name="state" type="text" class="mt-1 block w-full"
                                :value="old('state')" />
                        </div>
                    </div>
                </div>

                {{-- CATALOG POLICY --}}
                <div class="p-8 bg-gray-900 rounded-[2rem] shadow-xl text-white">
                    <x-input-label for="catalog_id" :value="__('Assign Product Whitelist Folder')"
                        class="text-blue-400 text-[10px] font-black uppercase mb-2" />
                    <select name="catalog_id" id="catalog_id"
                        class="w-full bg-gray-800 border-gray-700 text-white rounded-xl text-sm focus:ring-blue-500">
                        <option value="">{{ __('Inherit Parent Catalog By Default') }} [8.a.2]</option>
                        @foreach ($catalogs as $catalog)
                            <option value="{{ $catalog->id }}"
                                {{ old('catalog_id') == $catalog->id ? 'selected' : '' }}>{{ $catalog->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('companys.index') }}"
                        class="text-xs font-black uppercase text-gray-400 py-4">{{ __('Cancel') }}</a>
                    <x-primary-button
                        class="bg-blue-600 hover:bg-blue-700 py-4 px-12 rounded-2xl text-[10px] font-black uppercase">
                        {{ __('Finalize Registration') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
