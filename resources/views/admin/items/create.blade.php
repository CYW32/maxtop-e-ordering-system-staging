<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Product Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- SKU -->
                        <div>
                            <x-input-label for="sku" :value="__('Item SKU')" />
                            <x-text-input id="sku" name="sku" type="text"
                                class="mt-1 block w-full uppercase" :value="old('sku')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('sku')" />
                        </div>

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Item Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Base Price (RM)')" />
                            <x-text-input id="price" name="price" type="number" step="0.01"
                                class="mt-1 block w-full" :value="old('price', '0.00')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <x-input-label for="image" :value="__('Product Image (Auto-compressed)')" />
                            <input type="file" name="image" id="image"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                accept="image/*">
                            <p class="text-xs text-gray-500 mt-1">Max 2MB. Supports JPG, PNG.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('image')" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" rows="4"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="flex items-center justify-end mt-6 border-t pt-4">
                        <a href="{{ route('items.index') }}"
                            class="text-sm text-gray-600 underline mr-4">{{ __('Cancel') }}</a>
                        <x-primary-button>
                            {{ __('Save Product Item') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
