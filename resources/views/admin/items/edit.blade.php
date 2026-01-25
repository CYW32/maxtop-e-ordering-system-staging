<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('⚙️ Edit Product: ') . $item->sku }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl sm:rounded-2xl border border-gray-100">

                <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="sku" :value="__('SKU Code')" />
                            <x-text-input id="sku" name="sku" type="text"
                                class="mt-1 block w-full uppercase font-mono" :value="old('sku', $item->sku)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('sku')" />
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Item Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $item->name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="price" :value="__('Unit Price (RM)')" />
                            <x-text-input id="price" name="price" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('price', $item->price)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        <div>
                            <x-input-label for="image" :value="__('Update Image (Max 2MB)')" />
                            <input type="file" id="image" name="image"
                                class="mt-1 block w-full text-xs text-gray-500 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                accept="image/*">
                        </div>
                    </div>

                    <div>
                        <x-input-label :value="__('Product Categories')" />
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach (\App\Models\Category::all() as $category)
                                <label
                                    class="flex items-center text-[10px] font-black uppercase text-gray-700 bg-gray-50 p-2 border rounded-lg cursor-pointer hover:bg-gray-100 transition shadow-sm">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                        class="rounded border-gray-300 text-blue-600 mr-2"
                                        {{ $item->categories->contains($category->id) ? 'checked' : '' }}>
                                    {{ $category->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Technical Description')" />
                        <textarea id="description" name="description" rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm">{{ old('description', $item->description) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                        <a href="{{ route('items.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 underline mr-6">{{ __('Cancel') }}</a>
                        <x-primary-button
                            class="bg-blue-700 hover:bg-blue-800">{{ __('Update Product Details') }}</x-primary-button>
                    </div>
                </form>

                {{-- Lifecycle Section: Fulfills Soft Deactivation Requirement --}}
                <div class="mt-12 pt-8 border-t-2 border-dashed border-gray-100">
                    <div
                        class="bg-gray-50 p-6 rounded-2xl border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div>
                            <h3 class="text-gray-800 font-black uppercase text-sm">
                                {{ __('Item Visibility & Lifecycle') }}</h3>
                            <p class="text-gray-500 text-[10px] mt-2 italic">
                                {{ __('Deactivated items are hidden from customers but remain in historical order records [Section 3.c].') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            {{-- Status Toggle Form --}}
                            <form action="{{ route('items.update', $item) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="sku" value="{{ $item->sku }}">
                                <input type="hidden" name="name" value="{{ $item->name }}">
                                <input type="hidden" name="price" value="{{ $item->price }}">

                                @if ($item->status === 'active')
                                    <input type="hidden" name="status" value="deactive">
                                    <button type="submit"
                                        class="px-6 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-black uppercase transition shadow-lg">
                                        {{ __('⛔ Deactivate Item') }}
                                    </button>
                                @else
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit"
                                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-black uppercase transition shadow-lg">
                                        {{ __('✅ Reactivate Item') }}
                                    </button>
                                @endif
                            </form>

                            {{-- Hard Delete Guard [Section 3.c] --}}
                            @if ($item->canBeDeleted())
                                <form action="{{ route('items.destroy', $item) }}" method="POST"
                                    onsubmit="return confirm('Hard delete this item permanently?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-xl text-xs font-black uppercase transition shadow-lg">
                                        {{ __('🔥 Hard Delete') }}
                                    </button>
                                </form>
                            @else
                                <div class="bg-white text-gray-400 px-4 py-2 rounded-xl text-[9px] font-black uppercase border border-gray-200 cursor-help"
                                    title="Locked due to order history.">
                                    {{ __('🔒 Deletion Locked') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
