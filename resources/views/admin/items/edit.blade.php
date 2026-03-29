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

            {{-- ERROR NOTIFICATION ALERT (包含重名阻挡的弹窗) --}}
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show"
                    class="bg-red-50 border border-red-200 p-6 rounded-2xl flex items-center justify-between gap-3 shadow-sm mb-6 transition-all">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-red-500 shadow-sm shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-black text-red-800 tracking-wide">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" type="button"
                        class="text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
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
                                <div>
                                    <x-input-label for="sku" :value="__('PRODUCT SKU (Locked)')"
                                        class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                    <input id="sku" type="text"
                                        class="block w-full border-gray-100 bg-gray-50 text-gray-400 rounded-2xl font-mono font-black uppercase cursor-not-allowed"
                                        value="{{ $item->sku }}" disabled />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="description" :value="__('Product Description')"
                                        class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                                    <textarea id="description" name="description" rows="3"
                                        class="block w-full border-gray-100 rounded-2xl text-xs font-bold uppercase focus:ring-blue-500">{{ old('description', $item->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Searchable Assignments Grid (Categories & Catalogs) 保留原样 --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                                x-data="{ search: '', count: {{ count(old('categories', $item->categories->pluck('id')->toArray())) }} }">
                                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-4">
                                    {{ __('Categorization') }} <span class="ml-2 text-blue-500 font-mono"
                                        x-text="count"></span>
                                </h3>
                                <div class="relative mb-4">
                                    <input type="text" x-model="search" placeholder="Filter categories..."
                                        class="w-full bg-gray-50 border-gray-100 rounded-2xl text-[10px] font-black uppercase pl-10 focus:ring-blue-500">
                                </div>
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                    @php $selectedIds = old('categories', $item->categories->pluck('id')->toArray()); @endphp
                                    @foreach ($categories as $category)
                                        <label
                                            x-show="'{{ strtoupper($category->name) }}'.includes(search.toUpperCase())"
                                            class="flex items-center p-2 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors group">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                {{ in_array($category->id, $selectedIds) ? 'checked' : '' }}
                                                x-on:change="count = $event.target.checked ? count + 1 : count - 1">
                                            <span
                                                class="ml-3 text-[11px] font-black text-gray-600 uppercase">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                                x-data="{ search: '', count: {{ count(old('catalogs', $item->catalogs->pluck('id')->toArray())) }} }">
                                <h3 class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-4">
                                    {{ __('Catalog Whitelist') }} <span class="ml-2 text-indigo-500 font-mono"
                                        x-text="count"></span>
                                </h3>
                                <div class="relative mb-4">
                                    <input type="text" x-model="search" placeholder="Filter catalogs..."
                                        class="w-full bg-gray-50 border-gray-100 rounded-2xl text-[10px] font-black uppercase pl-10 focus:ring-indigo-500">
                                </div>
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                    @php $selectedCatalogIds = old('catalogs', $item->catalogs->pluck('id')->toArray()); @endphp
                                    @foreach ($catalogs as $catalog)
                                        <label
                                            x-show="'{{ strtoupper($catalog->name) }}'.includes(search.toUpperCase())"
                                            class="flex items-center p-2 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors group">
                                            <input type="checkbox" name="catalogs[]" value="{{ $catalog->id }}"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                {{ in_array($catalog->id, $selectedCatalogIds) ? 'checked' : '' }}
                                                x-on:change="count = $event.target.checked ? count + 1 : count - 1">
                                            <span
                                                class="ml-3 text-[11px] font-black text-gray-600 uppercase">{{ $catalog->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- COLUMN 2: STATUS & MULTI-MEDIA --}}
                    <div class="space-y-8">
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                            <div class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest">Operational
                                Status</div>
                            <select name="status"
                                class="w-full border-gray-100 rounded-2xl text-xs font-black uppercase text-gray-700 focus:ring-blue-500 shadow-sm">
                                <option value="active" @if ($item->status === 'active') selected @endif>Active &
                                    Orderable</option>
                                <option value="inactive" @if ($item->status === 'inactive') selected @endif>Deactivated
                                    (Hold)</option>
                            </select>
                        </div>

                        {{-- 新加：MULTI-IMAGE MEDIA CARD --}}
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm"
                            x-data="{
                                existingImages: {{ json_encode($item->images ?? ($item->image_path ? [$item->image_path] : [])) }},
                                newPreviews: [],
                                removeExisting(index) {
                                    // 把删掉的图加进 hidden input，等下发给 controller 删掉
                                    let removed = this.existingImages.splice(index, 1);
                                    let input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'remove_images[]';
                                    input.value = removed[0];
                                    $refs.deletionForm.appendChild(input);
                                },
                                filesChosen(event) {
                                    this.newPreviews = [];
                                    const files = event.target.files;
                                    for (let i = 0; i < files.length; i++) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => { this.newPreviews.push(e.target.result); };
                                        reader.readAsDataURL(files[i]);
                                    }
                                }
                            }">

                            <div
                                class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest flex justify-between items-center">
                                <span>Product Media (Max 5)</span>
                            </div>

                            <div x-ref="deletionForm"></div> {{-- 放隐藏的删除标签 --}}

                            {{-- 已有图片的网格 --}}
                            <div class="grid grid-cols-2 gap-4 mb-4" x-show="existingImages.length > 0">
                                <template x-for="(img, index) in existingImages" :key="'ex-' + index">
                                    <div
                                        class="relative w-full aspect-square rounded-2xl border border-gray-100 overflow-hidden group bg-white p-1">
                                        <img :src="'/storage/' + img" class="w-full h-full object-contain">
                                        <button type="button" @click="removeExisting(index)"
                                            class="absolute top-2 right-2 bg-red-500 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <div x-show="index === 0"
                                            class="absolute bottom-2 left-2 bg-blue-500 text-white text-[8px] font-black uppercase px-2 py-1 rounded-md">
                                            Main</div>
                                    </div>
                                </template>
                            </div>

                            {{-- 新上传图片的网格 --}}
                            <div class="grid grid-cols-2 gap-4 mb-4" x-show="newPreviews.length > 0">
                                <template x-for="(preview, index) in newPreviews" :key="'new-' + index">
                                    <div
                                        class="relative w-full aspect-square rounded-2xl border-2 border-blue-400 overflow-hidden bg-blue-50/50 p-1">
                                        <img :src="preview" class="w-full h-full object-contain">
                                        <div
                                            class="absolute top-2 left-2 bg-blue-500 text-white text-[8px] font-black uppercase px-2 py-1 rounded-md">
                                            New</div>
                                    </div>
                                </template>
                            </div>

                            {{-- 上传按钮 --}}
                            <div class="relative w-full border-2 border-dashed border-gray-200 rounded-2xl hover:border-blue-400 hover:bg-blue-50/50 transition-colors flex flex-col items-center justify-center cursor-pointer p-6"
                                @click="$refs.fileInput.click()">
                                <input type="file" name="images[]" multiple x-ref="fileInput"
                                    @change="filesChosen" class="hidden" accept="image/jpeg, image/png, image/webp">
                                <svg class="w-6 h-6 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span
                                    class="text-[10px] font-black text-gray-600 uppercase text-center leading-relaxed">Add
                                    More Images<br><span class="text-gray-400 text-[8px] tracking-widest">Recommended:
                                        800x800px</span></span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- UOM CONFIGURATIONS (保留原样) --}}
                <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm" x-data="{
                    uoms: {{ $item->uoms->map(fn($u) => ['id' => $u->id, 'uom_name' => $u->uom_name, 'rate_qty' => $u->rate_qty, 'price' => $u->price, 'status' => $u->status])->toJson() }},
                    addUom() { this.uoms.push({ id: null, uom_name: '', rate_qty: 1, price: 0.00, status: 'active' }); },
                    removeUom(index) { this.uoms.splice(index, 1); }
                }">
                    <div class="flex justify-between items-center mb-10">
                        <div>
                            <h3
                                class="text-[11px] font-black uppercase text-gray-500 tracking-widest flex items-center gap-2">
                                Unit of Measure (UOM) Configurations</h3>
                        </div>
                        <button type="button" x-on:click="addUom()"
                            class="bg-blue-50 text-blue-600 px-6 py-2.5 rounded-2xl text-[10px] font-black uppercase">+
                            Add UOM</button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-50">
                            <thead>
                                <tr class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">
                                    <th class="px-2 pb-6 text-left">Unit Name</th>
                                    <th class="px-2 pb-6 text-center">Rate (Base 1)</th>
                                    <th class="px-2 pb-6 text-right">Price (RM)</th>
                                    <th class="px-2 pb-6 text-center">Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <template x-for="(uom, index) in uoms" :key="index">
                                    <tr class="group hover:bg-gray-50/50 transition-colors">
                                        <td class="px-2 py-6"><input type="hidden" :name="'uoms[' + index + '][id]'"
                                                x-model="uom.id"><input type="text"
                                                :name="'uoms[' + index + '][uom_name]'" x-model="uom.uom_name" required
                                                class="w-full border-gray-100 rounded-xl text-[11px] font-black uppercase">
                                        </td>
                                        <td class="px-2 py-6"><input type="number"
                                                :name="'uoms[' + index + '][rate_qty]'" x-model="uom.rate_qty"
                                                min="1" required
                                                class="w-24 mx-auto block border-gray-100 rounded-xl text-[11px] font-mono font-black text-blue-600 text-center">
                                        </td>
                                        <td class="px-2 py-6"><input type="number"
                                                :name="'uoms[' + index + '][price]'" x-model="uom.price"
                                                step="0.01" min="0" required
                                                class="w-32 ml-auto block border-gray-100 rounded-xl text-[11px] font-mono font-black text-right">
                                        </td>
                                        <td class="px-2 py-6"><select :name="'uoms[' + index + '][status]'"
                                                x-model="uom.status"
                                                class="w-full border-gray-100 rounded-xl text-[9px] font-black uppercase">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select></td>
                                        <td class="px-2 py-6"><button type="button" x-on:click="removeUom(index)"
                                                class="text-red-300 hover:text-red-500 transition-all p-2"><svg
                                                    class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg></button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SUBMIT BAR --}}
                <div class="flex items-center justify-end gap-6 pt-8">
                    <x-primary-button
                        class="bg-gray-900 text-white px-8 py-3 rounded-2xl text-[11px] font-black uppercase">Save
                        Product Configuration</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
