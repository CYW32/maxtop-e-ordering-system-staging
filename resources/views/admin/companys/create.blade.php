<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ $preselectedParent ? __('Add Branch to ') . $preselectedParent->company_name : __('Register New Business') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{
                isBranch: {{ $preselectedParent ? 'true' : 'false' }},
                parentId: '{{ $preselectedParent->id ?? '' }}'
            }"
                class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">

                <form method="POST" action="{{ route('companys.store') }}" class="space-y-8">
                    @csrf

                    <!-- STEP 1: DEFINE ENTITY TYPE [Addendum 3.c] -->
                    <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100">
                        <h3 class="text-[10px] font-black uppercase text-blue-600 mb-4 tracking-widest">
                            {{ __('Business Entity Type') }}</h3>

                        <div class="flex gap-8">
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" x-model="isBranch" :value="false"
                                    class="w-4 h-4 text-blue-600" @if ($preselectedParent) disabled @endif>
                                <span
                                    class="ml-2 text-xs font-black text-gray-700 uppercase {{ $preselectedParent ? 'opacity-50' : '' }}">{{ __('Main HQ Company') }}</span>
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" x-model="isBranch" :value="true"
                                    class="w-4 h-4 text-blue-600"
                                    @if ($preselectedParent) checked disabled @endif>
                                <span
                                    class="ml-2 text-xs font-black text-gray-700 uppercase">{{ __('Branch Office') }}</span>
                            </label>
                        </div>

                        {{-- HQ Selection Logic --}}
                        <div x-show="isBranch" x-transition class="mt-6">
                            <x-input-label for="parent_id" :value="__('Select Parent HQ')" class="text-[10px] uppercase font-black" />
                            <select name="parent_id" x-model="parentId"
                                class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm text-sm"
                                @if ($preselectedParent) readonly @endif>
                                <option value="">{{ __('-- Choose HQ --') }}</option>
                                @foreach ($hqs as $hq)
                                    <option value="{{ $hq->id }}">{{ $hq->company_name }}
                                        ({{ $hq->company_code }})</option>
                                @endforeach
                            </select>
                            {{-- Force hidden input if locked --}}
                            @if ($preselectedParent)
                                <input type="hidden" name="parent_id" value="{{ $preselectedParent->id }}">
                            @endif
                        </div>
                    </div>

                    <!-- STEP 2: LOGISTICS & CODES [Addendum 1.d] -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div x-show="!isBranch">
                            <x-input-label for="company_code" :value="__('Company Code (HQ)')"
                                class="text-[10px] font-black uppercase" />
                            <x-text-input name="company_code" type="text"
                                class="mt-1 block w-full uppercase font-mono" placeholder="MA-XXXX" />
                            <x-input-error :messages="$errors->get('company_code')" class="mt-2" />
                        </div>

                        <div x-show="isBranch">
                            <x-input-label for="branch_code" :value="__('Branch Code')"
                                class="text-[10px] font-black uppercase" />
                            <x-text-input name="branch_code" type="text"
                                class="mt-1 block w-full uppercase font-mono" placeholder="BR-XXXX" />
                            <x-input-error :messages="$errors->get('branch_code')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company_name" :value="__('Registered Business Name')"
                                class="text-[10px] font-black uppercase" />
                            <x-text-input name="company_name" type="text" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="catalog_id" :value="__('Assign Order Catalog')"
                                class="text-[10px] font-black uppercase" />
                            <select name="catalog_id"
                                class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm text-sm">
                                <option value="">{{ __('No specific catalog (Inherit from HQ)') }}</option>
                                @foreach ($catalogs as $catalog)
                                    <option value="{{ $catalog->id }}">{{ $catalog->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="company_reg_no" :value="__('SSM / Registration No.')"
                                class="text-[10px] font-black uppercase" />
                            <x-text-input name="company_reg_no" type="text" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="delivery_address" :value="__('Primary Delivery Address')"
                            class="text-[10px] font-black uppercase" />
                        <textarea name="delivery_address" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm" rows="3"
                            required></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end pt-6 border-t border-gray-100 gap-4">
                        <a href="{{ route('companys.index') }}"
                            class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition">{{ __('Cancel') }}</a>
                        <x-primary-button
                            class="bg-blue-600 hover:bg-blue-700 py-3 px-8 rounded-2xl shadow-lg transition-all uppercase text-[10px] font-black">
                            {{ __('Register Entity') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
