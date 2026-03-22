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
            <a href="{{ route('items.index') }}"
                class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back To Product Items') }}
            </a>
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
                                    <x-input-label for="name" :value="__('PRODUCT SKU')"
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- CATEGORIZATION SEARCH --}}
                            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                                x-data="{ search: '', count: {{ (int) $item->categories->count() }} }">
                                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-4">
                                    {{ __('Categorization') }} <span class="ml-2 text-blue-500" x-text="count"></span>
                                </h3>
                                <input type="text" x-model="search" placeholder="{{ __('Filter...') }}"
                                    class="w-full mb-4 bg-gray-50 border-gray-100 rounded-2xl text-[10px] font-black uppercase pl-4">
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                    @php $itemCatIds = $item->categories->pluck('id')->toArray(); @endphp
                                    @forelse($categories as $category)
                                        <label
                                            x-show="'{{ strtoupper($category->name) }}'.includes(search.toUpperCase())"
                                            class="flex items-center p-2 rounded-xl hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                class="rounded border-gray-300 text-blue-600"
                                                @if (in_array($category->id, $itemCatIds)) checked @endif
                                                x-on:change="count = $event.target.checked ? count + 1 : count - 1">
                                            <span
                                                class="ml-3 text-[11px] font-black text-gray-700 uppercase">{{ $category->name }}</span>
                                        </label>
                                    @empty
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">
                                            {{ __('No Groups Defined') }}</p>
                                    @endforelse
                                </div>
                            </div>

                            {{-- CATALOG WHITELIST SEARCH [Backbone 3.a.1] --}}
                            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                                x-data="{ search: '', count: {{ (int) $item->catalogs->count() }} }">
                                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-4">
                                    {{ __('Catalog Whitelist') }} <span class="ml-2 text-indigo-500"
                                        x-text="count"></span></h3>
                                <input type="text" x-model="search" placeholder="{{ __('Filter...') }}"
                                    class="w-full mb-4 bg-gray-50 border-gray-100 rounded-2xl text-[10px] font-black uppercase pl-4">
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                    @php $itemCatalogIds = $item->catalogs->pluck('id')->toArray(); @endphp
                                    @forelse($catalogs as $catalog)
                                        <label
                                            x-show="'{{ strtoupper($catalog->name) }}'.includes(search.toUpperCase())"
                                            class="flex items-center p-2 rounded-xl hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="catalogs[]" value="{{ $catalog->id }}"
                                                class="rounded border-gray-300 text-indigo-600"
                                                @if (in_array($catalog->id, $itemCatalogIds)) checked @endif
                                                x-on:change="count = $event.target.checked ? count + 1 : count - 1">
                                            <span
                                                class="ml-3 text-[11px] font-black text-gray-700 uppercase">{{ $catalog->name }}</span>
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

                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                            x-data="{
                                imagePreview: '{{ $item->image_path ? asset('storage/' . $item->image_path) : '' }}',
                                fileChosen(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            this.imagePreview = e.target.result;
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                }
                            }">

                            <div
                                class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest flex justify-between items-center">
                                <span>{{ __('Product Media') }}</span>
                            </div>

                            {{-- INTERACTIVE DROPZONE --}}
                            <div class="relative w-full aspect-square border-2 border-dashed border-gray-200 rounded-[2rem] hover:border-blue-400 hover:bg-blue-50/50 transition-colors overflow-hidden group flex flex-col items-center justify-center cursor-pointer"
                                @click="$refs.fileInput.click()">

                                <input type="file" name="image" x-ref="fileInput" @change="fileChosen"
                                    class="hidden" accept="image/jpeg, image/png, image/webp">

                                <div x-show="!imagePreview"
                                    class="flex flex-col items-center justify-center p-6 text-center">
                                    <div
                                        class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 mb-4 group-hover:bg-white group-hover:text-blue-500 transition-colors shadow-sm">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-[11px] font-black text-gray-600 uppercase">{{ __('Click to upload image') }}</span>
                                    <span
                                        class="text-[8px] font-bold text-gray-400 mt-2 uppercase tracking-widest">{{ __('PNG, JPG, WEBP') }}</span>
                                </div>

                                <div x-show="imagePreview" class="absolute inset-0 w-full h-full"
                                    style="display: none;">
                                    <img :src="imagePreview" class="w-full h-full object-cover">

                                    <div
                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center backdrop-blur-[2px]">
                                        <div
                                            class="bg-white/90 text-gray-900 text-[10px] font-black uppercase tracking-widest px-6 py-3 rounded-xl shadow-lg flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                            </svg>
                                            {{ __('Replace Image') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- REFACTORED: Unit of Measure (UOM) Configurations container [CRUD FIX] --}}
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
                                class="text-[10px] font-black uppercase text-gray-400 tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                {{ __('Unit of Measure (UOM) Configurations') }}
                            </h3>
                            <p class="text-[8px] font-bold text-gray-300 uppercase italic mt-1">
                                {{ __('Pricing source for all orders.') }} [Addendum 5.a]</p>
                        </div>
                        <button type="button" x-on:click="addUom()"
                            class="bg-blue-50 text-blue-600 px-6 py-2 rounded-xl text-[9px] font-black uppercase hover:bg-blue-100 transition">
                            {{ __('+ Add UOM') }}
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-50">
                            <thead>
                                <tr class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">
                                    <th class="px-4 py-4 text-left">{{ __('Unit Name') }}</th>
                                    <th class="px-4 py-4 text-center">{{ __('Rate (Base 1)') }}</th>
                                    <th class="px-4 py-4 text-right">{{ __('Internal Price (RM)') }}</th>
                                    <th class="px-4 py-4 text-center">{{ __('Status') }}</th>
                                    <th class="px-4 py-4 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <template x-for="(uom, index) in uoms" :key="index">
                                    <tr class="group hover:bg-gray-50/50 transition-colors">
                                        <td class="px-2 py-4">
                                            <input type="hidden" :name="'uoms[' + index + '][id]'" x-model="uom.id">
                                            <input type="text" :name="'uoms[' + index + '][uom_name]'"
                                                x-model="uom.uom_name" required
                                                class="w-full border-gray-100 rounded-xl text-[11px] font-black uppercase focus:ring-blue-500">
                                        </td>
                                        <td class="px-2 py-4">
                                            <input type="number" :name="'uoms[' + index + '][rate_qty]'"
                                                x-model="uom.rate_qty" min="1" required
                                                class="w-24 mx-auto block border-gray-100 rounded-xl text-[11px] font-mono font-black text-blue-600 text-center">
                                        </td>
                                        <td class="px-2 py-4">
                                            <input type="number" :name="'uoms[' + index + '][price]'"
                                                x-model="uom.price" step="0.01" min="0" required
                                                class="w-32 ml-auto block border-gray-100 rounded-xl text-[11px] font-mono font-black text-right">
                                        </td>
                                        <td class="px-2 py-4">
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
                                        <td class="px-2 py-4 text-right">
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

                <div class="flex items-center justify-end gap-6 pt-8">
                    <a href="{{ route('items.index') }}"
                        class="text-[10px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">{{ __('Discard Changes') }}</a>
                    <x-primary-button
                        class="bg-gray-900 hover:bg-black py-5 px-16 rounded-[2rem] shadow-xl shadow-gray-100 transition-all uppercase text-[11px] font-black tracking-[0.1em]">
                        {{ __('Save Product Configuration') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
