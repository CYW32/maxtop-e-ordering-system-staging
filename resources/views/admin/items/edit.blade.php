<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Edit Product Entity') }}: <span class="text-blue-600">{{ $item->sku }}</span>
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
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ARCHITECTURE GUARD: Transactional Integrity Warning [Backbone 3.c.1] --}}
            @if ($item->orderItems()->whereHas('order', fn($q) => $q->whereIn('status', ['approved', 'completed']))->exists())
                <div class="p-6 bg-amber-50 border border-amber-100 rounded-[2rem] flex gap-4 items-center">
                    <div
                        class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-amber-500 shadow-sm">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-[11px] font-black uppercase text-amber-900 tracking-tight">
                            {{ __('Core Identity Lock Active') }}</h4>
                        <p class="text-[9px] font-bold text-amber-700 uppercase italic">
                            {{ __('Finalized transaction snapshots exist for this item. Identity modifications are restricted to maintain historical auditing integrity.') }}
                            [3.c.1, 4.b]</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    {{-- COLUMN 1: PRIMARY IDENTITY & WHITELISTING --}}
                    <div class="lg:col-span-2 space-y-8">

                        <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="name" :value="__('PRODUCT NAME')"
                                        class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                    <x-text-input id="name" name="name" type="text"
                                        class="block w-full font-bold uppercase" :value="old('name', $item->name)" required />
                                </div>

                                {{-- FRONTEND LOCK: System SKU --}}
                                <div>
                                    <x-input-label for="sku" :value="__('PRODUCT SKU (Locked)')"
                                        class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                    <div class="relative">
                                        <input id="sku" type="text"
                                            class="block w-full border-gray-100 bg-gray-50 text-gray-400 rounded-2xl font-mono font-black uppercase cursor-not-allowed shadow-none focus:ring-0"
                                            value="{{ $item->sku }}" disabled readonly />
                                        {{-- Lock Icon to visually indicate it cannot be changed --}}
                                        <svg class="w-4 h-4 text-gray-300 absolute right-4 top-1/2 -translate-y-1/2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="description" :value="__('Product Description')"
                                        class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                    <textarea id="description" name="description" rows="3"
                                        class="block w-full border-gray-100 rounded-2xl text-xs font-bold uppercase focus:ring-blue-500">{{ old('description', $item->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Searchable Assignments Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            {{-- CATEGORIZATION SEARCH --}}
                            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                                x-data="{
                                    search: '',
                                    count: {{ count(old('categories', $item->categories->pluck('id')->toArray())) }}
                                }">
                                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-4">
                                    {{ __('Categorization') }} <span class="ml-2 text-blue-500 font-mono"
                                        x-text="count"></span>
                                </h3>

                                {{-- Search Input with Icon --}}
                                <div class="relative mb-4">
                                    <input type="text" x-model="search"
                                        placeholder="{{ __('Filter categories...') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-2xl text-[10px] font-black uppercase pl-10 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <svg class="w-4 h-4 text-gray-300 absolute left-3 top-1/2 -translate-y-1/2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                    @php
                                        // Merge existing category IDs with any old input from a failed validation
                                        $selectedIds = old('categories', $item->categories->pluck('id')->toArray());
                                    @endphp

                                    @forelse($categories as $category)
                                        <label
                                            x-show="'{{ strtoupper($category->name) }}'.includes(search.toUpperCase())"
                                            class="flex items-center p-2 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors group">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                {{ in_array($category->id, $selectedIds) ? 'checked' : '' }}
                                                x-on:change="count = $event.target.checked ? count + 1 : count - 1">
                                            <span
                                                class="ml-3 text-[11px] font-black text-gray-600 uppercase group-hover:text-gray-900 transition-colors">
                                                {{ $category->name }}
                                            </span>
                                        </label>
                                    @empty
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">
                                            {{ __('No Groups Defined') }}
                                        </p>
                                    @endforelse
                                </div>
                            </div>

                            {{-- CATALOG WHITELIST SEARCH --}}
                            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                                x-data="{
                                    search: '',
                                    count: {{ count(old('catalogs', $item->catalogs->pluck('id')->toArray())) }}
                                }">

                                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-4">
                                    {{ __('Catalog Whitelist') }}
                                    <span class="ml-2 text-indigo-500 font-mono" x-text="count"></span>
                                </h3>

                                {{-- Search Input with SVG Icon --}}
                                <div class="relative mb-4">
                                    <input type="text" x-model="search" placeholder="{{ __('Filter catalogs...') }}"
                                        class="w-full bg-gray-50 border-gray-100 rounded-2xl text-[10px] font-black uppercase pl-10 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                    <svg class="w-4 h-4 text-gray-300 absolute left-3 top-1/2 -translate-y-1/2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                    @php
                                        // Pre-calculate selected IDs from old input OR database
                                        $selectedCatalogIds = old('catalogs', $item->catalogs->pluck('id')->toArray());
                                    @endphp

                                    @forelse($catalogs as $catalog)
                                        <label
                                            x-show="'{{ strtoupper($catalog->name) }}'.includes(search.toUpperCase())"
                                            class="flex items-center p-2 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors group">
                                            <input type="checkbox" name="catalogs[]" value="{{ $catalog->id }}"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                {{ in_array($catalog->id, $selectedCatalogIds) ? 'checked' : '' }}
                                                x-on:change="count = $event.target.checked ? count + 1 : count - 1">
                                            <span
                                                class="ml-3 text-[11px] font-black text-gray-600 uppercase group-hover:text-gray-900 transition-colors">
                                                {{ $catalog->name }}
                                            </span>
                                        </label>
                                    @empty
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">
                                            {{ __('No Catalogs Defined') }}
                                        </p>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- COLUMN 2: STATUS & MEDIA --}}
                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                            <div class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest">
                                {{ __('Operational Status') }}</div>
                            <select name="status"
                                class="w-full border-gray-100 rounded-2xl text-xs font-black uppercase text-gray-700 focus:ring-blue-500 shadow-sm">
                                <option value="active" @if ($item->status === 'active') selected @endif>
                                    {{ __('Active & Orderable') }}</option>
                                <option value="inactive" @if ($item->status === 'inactive') selected @endif>
                                    {{ __('Deactivated (Hold)') }}</option>
                            </select>
                        </div>

                        {{-- Interactive Media Card --}}
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                            x-data="{
                                existingImages: {{ json_encode(is_array($item->image_path) ? $item->image_path : ($item->image_path ? [$item->image_path] : [])) }},
                                newPreviews: [],
                                viewingImage: null, // Holds the image URL for the popup modal
                            
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
                            
                                removeExisting(index) {
                                    this.existingImages.splice(index, 1);
                                },
                            
                                removeNew(index) {
                                    this.newPreviews.splice(index, 1);
                                    // Remove the file from the actual hidden input so it doesn't upload
                                    const dt = new DataTransfer();
                                    const input = this.$refs.fileInput;
                                    for (let i = 0; i < input.files.length; i++) {
                                        if (i !== index) dt.items.add(input.files[i]);
                                    }
                                    input.files = dt.files;
                                }
                            }">

                            {{-- FULL SCREEN IMAGE MODAL --}}
                            <div x-show="viewingImage" style="display: none;"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/80 backdrop-blur-sm p-4 md:p-8"
                                x-transition.opacity @click="viewingImage = null">

                                {{-- Fixed Modal Box --}}
                                <div class="relative w-full max-w-4xl bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col"
                                    @click.stop>

                                    {{-- Top Bar with Close Button --}}
                                    <div class="flex justify-between items-center px-8 py-5 border-b border-gray-100">
                                        <h3 class="text-[11px] font-black uppercase text-gray-500 tracking-widest">
                                            {{ __('Image Preview') }}</h3>
                                        <button type="button" @click="viewingImage = null"
                                            class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-xl transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Fixed Display Section --}}
                                    <div
                                        class="w-full h-[65vh] bg-gray-50/50 flex items-center justify-center p-6 relative checkerboard-bg">
                                        <img :src="viewingImage"
                                            class="max-w-full max-h-full object-contain drop-shadow-lg rounded-xl">
                                    </div>

                                    {{-- Bottom Bar --}}
                                    <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex justify-end">
                                        <button type="button" @click="viewingImage = null"
                                            class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-100 px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all shadow-sm">
                                            {{ __('Close Preview') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest flex justify-between items-center">
                                <span>{{ __('Product Media') }}</span>
                            </div>

                            {{-- INTERACTIVE DROPZONE --}}
                            <div class="relative w-full border-2 border-dashed border-gray-200 rounded-[2rem] hover:border-blue-400 hover:bg-blue-50/50 transition-colors overflow-hidden flex flex-col items-center justify-center cursor-pointer p-4 min-h-[200px]"
                                @click="$refs.fileInput.click()">

                                <input type="file" name="images[]" multiple x-ref="fileInput"
                                    @change="filesChosen" class="hidden" accept="image/jpeg, image/png, image/webp">

                                {{-- Default State (No Images) --}}
                                <div x-show="existingImages.length === 0 && newPreviews.length === 0"
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

                                {{-- Grid for Multiple Images --}}
                                <div class="grid grid-cols-3 gap-4 w-full">

                                    {{-- Existing Images from Database --}}
                                    <template x-for="(img, index) in existingImages" :key="'exist-' + index">
                                        <div class="relative aspect-square rounded-xl overflow-hidden shadow-sm group">
                                            {{-- HIDDEN INPUT: Tells backend to KEEP this image --}}
                                            <input type="hidden" name="existing_images[]" :value="img">

                                            <img :src="'/storage/' + img" class="w-full h-full object-cover">

                                            {{-- Hover Actions (View & Delete) --}}
                                            <div
                                                class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 backdrop-blur-[2px]">
                                                <button type="button" @click.stop="viewingImage = '/storage/' + img"
                                                    class="p-2 bg-white/90 text-gray-800 rounded-full hover:text-blue-600 transition shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button type="button" @click.stop="removeExisting(index)"
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

                                    {{-- New Selected Previews --}}
                                    <template x-for="(preview, index) in newPreviews" :key="'new-' + index">
                                        <div
                                            class="relative aspect-square rounded-xl overflow-hidden shadow-sm border-2 border-blue-400 group">
                                            <img :src="preview" class="w-full h-full object-cover">

                                            {{-- Hover Actions (View & Delete) --}}
                                            <div
                                                class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 backdrop-blur-[2px]">
                                                <button type="button" @click.stop="viewingImage = preview"
                                                    class="p-2 bg-white/90 text-gray-800 rounded-full hover:text-blue-600 transition shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
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
                    uoms: {{ $item->uoms->map(
                            fn($u) => [
                                'id' => $u->id,
                                'uom_name' => $u->uom_name,
                                'rate_qty' => $u->rate_qty,
                                'price' => $u->price,
                                'status' => $u->status,
                            ],
                        )->toJson() }},
                    addUom() {
                        this.uoms.push({ id: null, uom_name: '', rate_qty: 1, price: 0.00, status: 'active' });
                    },
                    removeUom(index) {
                        this.uoms.splice(index, 1);
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
                                {{ __('Pricing source for all orders.') }} [Addendum 5.a]</p>
                        </div>
                        <button type="button" x-on:click="addUom()"
                            class="bg-blue-50 text-blue-600 px-6 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-wider hover:bg-blue-100 transition-all shadow-sm">
                            {{ __('+ Add UOM') }}
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-50">
                            <thead>
                                <tr class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">
                                    <th
                                        class="px-2 pb-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Unit Name') }}</th>
                                    <th
                                        class="px-2 pb-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Rate (Base 1)') }}</th>
                                    <th
                                        class="px-2 pb-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Internal Price (RM)') }}</th>
                                    <th
                                        class="px-2 pb-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ __('Status') }}</th>
                                    <th
                                        class="px-2 pb-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <template x-for="(uom, index) in uoms" :key="index">
                                    <tr class="group hover:bg-gray-50/50 transition-colors">
                                        <td class="px-2 py-6 align-top">
                                            <input type="hidden" :name="'uoms[' + index + '][id]'" x-model="uom.id">
                                            <input type="text" :name="'uoms[' + index + '][uom_name]'"
                                                x-model="uom.uom_name" required
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
                                                <option value="active">{{ __('Active') }}</option>
                                                <option value="inactive">{{ __('Inactive') }}</option>
                                            </select>

                                            {{-- ARCHITECTURE STANDARD: Visible validation feedback --}}
                                            @error('uoms.*.status')
                                                <p class="text-[7px] text-red-500 mt-1 uppercase font-black">
                                                    {{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-2 py-6 align-top text-right">
                                            <button type="button" x-on:click="removeUom(index)"
                                                class="text-red-300 hover:text-red-500 transition-all p-2">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
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

                        <div x-show="uoms.length === 0"
                            class="py-12 text-center border-2 border-dashed border-gray-100 rounded-[2rem] bg-gray-50/30">
                            <p class="text-[9px] font-black text-gray-300 uppercase italic">
                                {{ __('No packaging units defined. This item will be suppressed from Catalogs until a valid unit is saved.') }}
                                [3.a.3]
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ADD BAR --}}
                <div class="flex items-center justify-end gap-6 pt-8">
                    <a href="{{ route('items.index') }}"
                        class="text-[10px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">{{ __('Discard Changes') }}</a>
                    <x-primary-button
                        class="w-full md:w-auto bg-gray-900 hover:bg-black text-white px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all shadow-md whitespace-nowrap">
                        {{ __('Save Product Configuration') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
