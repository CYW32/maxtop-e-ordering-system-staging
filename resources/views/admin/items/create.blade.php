<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('Register New Product') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        uoms: [],
        addUom() {
            this.uoms.push({ uom_name: '', rate_qty: 1, price: 0, status: 'active' });
        },
        removeUom(index) {
            this.uoms.splice(index, 1);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- SECTION 1: MASTER ATTRIBUTES --}}
                <div
                    class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="sku" :value="__('Product SKU')"
                                    class="text-[10px] font-black uppercase text-gray-400" />
                                <x-text-input id="sku" name="sku" type="text"
                                    class="mt-1 block w-full font-mono uppercase" :value="old('sku')" required />
                                <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Listing Status')"
                                    class="text-[10px] font-black uppercase text-gray-400" />
                                <select name="status"
                                    class="mt-1 block w-full rounded-xl border-gray-300 text-sm font-bold uppercase focus:ring-blue-500">
                                    <option value="active">{{ __('Active') }}</option>
                                    <option value="deactive">{{ __('Inactive') }}</option>
                                </select>
                                <p class="mt-2 text-[9px] text-amber-600 font-black uppercase italic">
                                    {{ __('Note: Status forced to Inactive if no UOM is added.') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Product Name')"
                                    class="text-[10px] font-black uppercase text-gray-400" />
                                <x-text-input id="name" name="name" type="text"
                                    class="mt-1 block w-full font-bold" :value="old('name')" required />
                            </div>
                        </div>

                        {{-- Universal Description: Fulfills Requirement --}}
                        <div>
                            <x-input-label for="description" :value="__('Product Description (Public)')"
                                class="text-[10px] font-black uppercase text-gray-400 mb-1" />
                            <textarea id="description" name="description" rows="3"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm"
                                placeholder="{{ __('Provide technical specs or context for customers...') }}">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- Image Asset Management --}}
                    <div
                        class="bg-gray-50 p-6 rounded-[1.5rem] border border-gray-100 flex flex-col items-center justify-center text-center">
                        <div
                            class="w-32 h-32 bg-white rounded-xl flex items-center justify-center mb-4 border-2 border-dashed border-gray-200 text-gray-300">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input id="image" name="image" type="file"
                            class="text-[10px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    </div>
                </div>

                {{-- SECTION 2: UNIT OF MEASURE (UOM) MANAGEMENT [Addendum 5.a] --}}
                <div class="bg-gray-900 rounded-[2.5rem] p-8 shadow-xl">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h3 class="text-xs font-black uppercase text-blue-400 tracking-widest">
                                {{ __('UOM Configurations (Mandatory)') }}</h3>
                            <p class="text-[10px] text-gray-500 mt-1 uppercase">
                                {{ __('Strict UOM Protocol: At least one active unit required for catalog visibility.') }}
                            </p>
                        </div>
                        <button type="button" @click="addUom()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[10px] font-black uppercase transition shadow-lg shadow-blue-900">
                            {{ __('+ Add Packaging Unit') }}
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(uom, index) in uoms" :key="index">
                            <div
                                class="grid grid-cols-1 md:grid-cols-12 gap-4 p-5 bg-gray-800 rounded-2xl border border-gray-700 items-end transition-all">
                                <div class="md:col-span-4">
                                    <label
                                        class="text-[9px] font-black text-gray-500 uppercase mb-1 block">{{ __('Unit Name (e.g. Carton 24s)') }}</label>
                                    <input type="text" :name="'uoms[' + index + '][uom_name]'" x-model="uom.uom_name"
                                        required
                                        class="w-full bg-gray-900 border-gray-700 rounded-xl text-white text-sm focus:ring-blue-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        class="text-[9px] font-black text-gray-500 uppercase mb-1 block">{{ __('Rate Qty') }}</label>
                                    <input type="number" :name="'uoms[' + index + '][rate_qty]'" x-model="uom.rate_qty"
                                        min="1" required
                                        class="w-full bg-gray-900 border-gray-700 rounded-xl text-white text-sm focus:ring-blue-500">
                                </div>
                                <div class="md:col-span-3">
                                    <label
                                        class="text-[9px] font-black text-blue-500 uppercase mb-1 block">{{ __('Staff Price (RM)') }}</label>
                                    <input type="number" step="0.01" :name="'uoms[' + index + '][price]'"
                                        x-model="uom.price" required
                                        class="w-full bg-gray-900 border-blue-900/50 rounded-xl text-white text-sm focus:ring-blue-500">
                                </div>
                                <div class="md:col-span-3 flex gap-2">
                                    <select :name="'uoms[' + index + '][status]'" x-model="uom.status"
                                        class="flex-1 rounded-xl text-[10px] font-black uppercase border-gray-700 bg-gray-900 focus:ring-blue-500"
                                        :class="uom.status === 'active' ? 'text-green-400' : 'text-red-400'">
                                        <option value="active">{{ __('Active') }}</option>
                                        <option value="inactive">{{ __('Inactive') }}</option>
                                    </select>
                                    <button type="button" @click="removeUom(index)"
                                        class="h-10 w-10 flex items-center justify-center bg-gray-700 hover:bg-red-600 rounded-xl text-white transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="uoms.length === 0"
                            class="py-10 border-2 border-dashed border-gray-800 rounded-[2rem] flex flex-col items-center justify-center">
                            <p class="text-[10px] font-black uppercase text-gray-600 tracking-widest">
                                {{ __('No packaging units defined') }}</p>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: ASSIGNMENTS (Categorization and Catalogs) --}}
                <div
                    class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Categories [Backbone 3.a.3] --}}
                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest">
                            {{ __('Categorization') }}</h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach (\App\Models\Category::all() as $category)
                                <label
                                    class="flex items-center p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition cursor-pointer">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                        class="rounded border-gray-300 text-blue-600">
                                    <span
                                        class="ml-2 text-[10px] font-black uppercase text-gray-600">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Catalog Whitelist Selection: Fulfills Requirement --}}
                    <div>
                        <h3 class="text-[10px] font-black uppercase text-blue-600 mb-6 tracking-widest">
                            {{ __('Catalog Visibility (Whitelisting)') }}</h3>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach ($catalogs as $catalog)
                                <label
                                    class="flex items-center p-3 border border-blue-50 rounded-xl hover:bg-blue-50 transition cursor-pointer">
                                    <input type="checkbox" name="catalogs[]" value="{{ $catalog->id }}"
                                        class="rounded border-blue-300 text-blue-600">
                                    <div class="ml-3 flex flex-col">
                                        <span
                                            class="text-[10px] font-black uppercase text-blue-900">{{ $catalog->name }}</span>
                                        <span
                                            class="text-[8px] text-blue-400 font-bold uppercase tracking-tighter">{{ __('Immediately whitelist item for this group') }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- SUBMIT ACTIONS --}}
                <div
                    class="flex items-center justify-end gap-4 p-6 bg-white rounded-3xl border border-gray-100 shadow-sm">
                    <a href="{{ route('items.index') }}"
                        class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">{{ __('Cancel') }}</a>
                    <x-primary-button
                        class="bg-blue-600 hover:bg-blue-700 px-10 py-4 rounded-2xl text-[10px] font-black uppercase shadow-lg shadow-blue-100">
                        {{ __('Create Product & Units') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
