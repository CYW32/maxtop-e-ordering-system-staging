<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Register New Product') }}
            </h2>
            <a href="{{ route('items.index') }}"
                class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back to Registry') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Back Link --}}
            <a href="{{ route('items.index') }}"
                class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back To Product Items') }}
            </a>

            {{-- Error Catcher --}}
            @if ($errors->any())
                <div class="p-6 bg-red-50 border border-red-100 rounded-[2.5rem] shadow-sm">
                    <ul class="list-disc list-inside text-[10px] font-black uppercase text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    {{-- COLUMN 1: PRIMARY IDENTITY & WHITELISTING --}}
                    <div class="lg:col-span-2 space-y-8">

                        {{-- Basic Identity Card --}}
                        <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <x-input-label for="sku" :value="__('PRODUCT SKU')"
                                        class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                    <x-text-input id="sku" name="sku" type="text"
                                        class="block w-full font-bold uppercase" :value="old('sku')"
                                        placeholder="E.G. MT-1001" required />
                                </div>

                                <div>
                                    <x-input-label for="name" :value="__('PRODUCT NAME')"
                                        class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                    <x-text-input id="name" name="name" type="text"
                                        class="block w-full font-bold uppercase border-gray-100" :value="old('name')"
                                        placeholder="ENTER NAME" required />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="description" :value="__('Product Description')"
                                        class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                    <textarea id="description" name="description" rows="3"
                                        class="block w-full border-gray-100 rounded-2xl text-xs font-bold uppercase focus:ring-blue-500"
                                        placeholder="PROVIDE SPECS OR CONTEXT...">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Searchable Assignments Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            {{-- CATEGORIZATION SEARCH --}}
                            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                                x-data="{
                                    search: '',
                                    count: {{ count(old('categories', [])) }}
                                }">
                                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-4">
                                    {{ __('Categorization') }} <span class="ml-2 text-blue-500 font-mono"
                                        x-text="count"></span>
                                </h3>

                                <div class="relative mb-4">
                                    <input type="text" x-model="search"
                                        placeholder="{{ __('Filter categories...') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-2xl text-[10px] font-black uppercase pl-10 focus:ring-blue-500 focus:border-blue-500 transition-all py-3">
                                    <svg class="w-4 h-4 text-gray-300 absolute left-3 top-1/2 -translate-y-1/2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                <div class="space-y-2 max-h-56 overflow-y-auto pr-2 custom-scrollbar">
                                    @forelse($categories as $category)
                                        <label
                                            x-show="'{{ strtoupper($category->name) }}'.includes(search.toUpperCase())"
                                            class="flex items-center p-3 rounded-2xl hover:bg-gray-50 cursor-pointer transition-colors group">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                class="rounded border-gray-200 text-blue-600 focus:ring-blue-500"
                                                {{ is_array(old('categories')) && in_array($category->id, old('categories')) ? 'checked' : '' }}
                                                x-on:change="count = $event.target.checked ? count + 1 : count - 1">
                                            <span
                                                class="ml-3 text-[11px] font-black text-gray-600 uppercase group-hover:text-gray-900 transition-colors">
                                                {{ $category->name }}
                                            </span>
                                        </label>
                                    @empty
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">
                                            {{ __('No Groups Defined') }}</p>
                                    @endforelse
                                </div>
                            </div>

                            {{-- CATALOG WHITELIST SEARCH --}}
                            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                                x-data="{
                                    search: '',
                                    count: {{ count(old('catalogs', [])) }}
                                }">

                                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-4">
                                    {{ __('Catalog Whitelist') }}
                                    <span class="ml-2 text-indigo-500 font-mono" x-text="count"></span>
                                </h3>

                                <div class="relative mb-4">
                                    <input type="text" x-model="search" placeholder="{{ __('Filter catalogs...') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-2xl text-[10px] font-black uppercase pl-10 focus:ring-indigo-500 focus:border-indigo-500 transition-all py-3">
                                    <svg class="w-4 h-4 text-gray-300 absolute left-3 top-1/2 -translate-y-1/2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                <div class="space-y-2 max-h-56 overflow-y-auto pr-2 custom-scrollbar">
                                    @forelse($catalogs as $catalog)
                                        <label
                                            x-show="'{{ strtoupper($catalog->name) }}'.includes(search.toUpperCase())"
                                            class="flex items-center p-3 rounded-2xl hover:bg-gray-50 cursor-pointer transition-colors group">
                                            <input type="checkbox" name="catalogs[]" value="{{ $catalog->id }}"
                                                class="rounded border-gray-200 text-indigo-600 focus:ring-indigo-500"
                                                {{ is_array(old('catalogs')) && in_array($catalog->id, old('catalogs')) ? 'checked' : '' }}
                                                x-on:change="count = $event.target.checked ? count + 1 : count - 1">
                                            <span
                                                class="ml-3 text-[11px] font-black text-gray-600 uppercase group-hover:text-gray-900 transition-colors">
                                                {{ $catalog->name }}
                                            </span>
                                        </label>
                                    @empty
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">
                                            {{ __('No Catalogs Defined') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- COLUMN 2: STATUS & MEDIA --}}
                    <div class="space-y-8">
                        {{-- Listing Status Card --}}
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                            <div class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest">
                                {{ __('Operational Status') }}</div>
                            <select name="status"
                                class="w-full border-gray-100 rounded-2xl text-xs font-black uppercase text-gray-700 focus:ring-blue-500 shadow-sm py-3 cursor-pointer">
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>
                                    {{ __('Active & Orderable') }}</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>
                                    {{ __('Deactivated (Hold)') }}</option>
                            </select>
                        </div>

                        {{-- Interactive Media Card (CREATE PAGE) --}}
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                            x-data="{
                                newPreviews: [],
                                viewingImage: null,
                            
                                filesChosen(event) {
                                    const files = event.target.files;
                                    this.newPreviews = [];
                                    Array.from(files).forEach(file => {
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            this.newPreviews.push(e.target.result);
                                        };
                                        reader.readAsDataURL(file);
                                    });
                                },
                            
                                removeNew(index) {
                                    this.newPreviews.splice(index, 1);
                                    const dt = new DataTransfer();
                                    const input = this.$refs.fileInput;
                                    for (let i = 0; i < input.files.length; i++) {
                                        if (i !== index) dt.items.add(input.files[i]);
                                    }
                                    input.files = dt.files;
                                }
                            }">

                            <div
                                class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest flex justify-between items-center">
                                <span>{{ __('Product Media') }}</span>
                            </div>

                            {{-- INTERACTIVE DROPZONE --}}
                            <div class="relative w-full border-2 border-dashed border-gray-200 rounded-[2rem] hover:border-blue-400 hover:bg-blue-50/50 transition-colors overflow-hidden flex flex-col items-center justify-center cursor-pointer p-4 min-h-[200px]"
                                @click="$refs.fileInput.click()">

                                {{-- CRITICAL: Array name images[] and multiple flag --}}
                                <input type="file" name="images[]" multiple x-ref="fileInput"
                                    @change="filesChosen" class="hidden" accept="image/jpeg, image/png, image/webp">

                                <div x-show="newPreviews.length === 0"
                                    class="flex flex-col items-center justify-center p-6 text-center group">
                                    <div
                                        class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 mb-4 group-hover:bg-white group-hover:text-blue-500 transition-colors shadow-sm">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-[11px] font-black text-gray-600 uppercase">{{ __('Click to upload images') }}</span>
                                </div>

                                <div class="grid grid-cols-3 gap-4 w-full">
                                    <template x-for="(preview, index) in newPreviews" :key="index">
                                        <div
                                            class="relative aspect-square rounded-xl overflow-hidden shadow-sm border-2 border-blue-400 group">
                                            <img :src="preview" class="w-full h-full object-cover">

                                            <div
                                                class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                                <button type="button" @click.stop="removeNew(index)"
                                                    class="p-2 bg-white/90 text-red-500 rounded-full hover:bg-red-500 hover:text-white transition shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- UOM CONFIGURATIONS --}}
                <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm" x-data="{
                    uoms: {{ json_encode(old('uoms', [['uom_name' => '', 'rate_qty' => 1, 'price' => 0.0, 'status' => 'active']])) }},
                    addUom() {
                        this.uoms.push({ uom_name: '', rate_qty: 1, price: 0.00, status: 'active' });
                    },
                    removeUom(index) {
                        if (this.uoms.length > 1) { this.uoms.splice(index, 1); }
                    }
                }">

                    <div class="flex justify-between items-center mb-10">
                        <div>
                            <h3
                                class="text-[11px] font-black uppercase text-gray-500 tracking-widest flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                {{ __('Unit of Measure (UOM) Configurations') }}
                            </h3>
                            <p class="text-[9px] font-bold text-gray-300 uppercase italic mt-1 ml-7">
                                {{ __('Pricing source for all orders. [Addendum 5.A]') }}</p>
                        </div>
                        <button type="button" x-on:click="addUom()"
                            class="bg-blue-50 text-blue-600 px-6 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-wider hover:bg-blue-100 transition-all shadow-sm">
                            {{ __('+ Add UOM') }}
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-50">
                            <thead>
                                <tr>
                                    <th
                                        class="px-2 pb-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Unit Name') }}</th>
                                    <th
                                        class="px-2 pb-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Rate (Base 1)') }}</th>
                                    <th
                                        class="px-2 pb-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Price (RM)') }}</th>
                                    <th
                                        class="px-2 pb-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Status') }}</th>
                                    <th class="px-2 pb-6 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <template x-for="(uom, index) in uoms" :key="index">
                                    <tr class="group hover:bg-gray-50/50 transition-colors">
                                        <td class="px-2 py-6 align-top">
                                            <input type="text" :name="'uoms[' + index + '][uom_name]'"
                                                x-model="uom.uom_name" required placeholder="E.G. PCS"
                                                class="w-full border-gray-100 rounded-xl text-[11px] font-black uppercase focus:ring-blue-500">
                                        </td>
                                        <td class="px-2 py-6 align-top">
                                            <input type="number" :name="'uoms[' + index + '][rate_qty]'"
                                                x-model="uom.rate_qty" min="1" required
                                                class="w-24 mx-auto block border-gray-100 rounded-xl text-[11px] font-mono font-black text-blue-600 text-center">
                                        </td>
                                        <td class="px-2 py-6 align-top">
                                            <input type="number" :name="'uoms[' + index + '][price]'"
                                                x-model="uom.price" step="0.01" min="0" required
                                                class="w-32 ml-auto block border-gray-100 rounded-xl text-[11px] font-mono font-black text-right">
                                        </td>
                                        <td class="px-2 py-6 align-top">
                                            <select :name="'uoms[' + index + '][status]'" x-model="uom.status"
                                                class="w-full border-gray-100 rounded-xl text-[9px] font-black uppercase focus:ring-blue-500">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </td>
                                        <td class="px-2 py-6 align-top text-right">
                                            <button type="button" x-on:click="removeUom(index)"
                                                class="mt-1 text-gray-300 hover:text-red-500 transition-all p-2 rounded-xl hover:bg-red-50">
                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SUBMIT BAR --}}
                <div class="flex items-center justify-end gap-6 pt-8"">
                    <a href="{{ route('items.index') }}"
                        class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">{{ __('Discard Changes') }}</a>
                    <x-primary-button
                        class="w-full md:w-auto bg-gray-900 hover:bg-black text-white px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all shadow-md whitespace-nowrap">
                        {{ __('Create Product Configuration') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
