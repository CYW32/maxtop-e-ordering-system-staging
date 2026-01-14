<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Catalog: ') . $catalog->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('catalogs.update', $catalog->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Catalog Name -->
                    <div class="mb-8">
                        <x-input-label for="name" :value="__('Catalog Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full md:w-1/2"
                            :value="old('name', $catalog->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Changing this name will affect all customers assigned to this catalog.') }}</p>
                    </div>

                    <hr class="my-8 border-gray-200">

                    <!-- Whitelist Logic Interface -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Item Whitelist Selection') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Select the products that should be visible to customers assigned to this catalog. Unchecked items will be hidden from their view.') }}
                        </p>

                        @if ($items->isEmpty())
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <p class="text-sm text-yellow-700">
                                    {{ __('No items found in the system. Please create items first.') }}
                                </p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($items as $item)
                                    <div
                                        class="relative flex items-start p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center h-5">
                                            <input id="item_{{ $item->id }}" name="items[]"
                                                value="{{ $item->id }}" type="checkbox"
                                                {{ in_array($item->id, $assignedItemIds) ? 'checked' : '' }}
                                                class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="item_{{ $item->id }}"
                                                class="font-medium text-gray-700 cursor-pointer">
                                                <span class="block">{{ $item->name }}</span>
                                                <span class="text-gray-500 text-xs uppercase tracking-tighter">SKU:
                                                    {{ $item->sku }}</span>
                                            </label>
                                            <div class="mt-1 text-xs font-semibold text-indigo-600">
                                                RM {{ number_format($item->price, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-8 border-t pt-6">
                        <a href="{{ route('catalogs.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button>
                            {{ __('Update Catalog & Whitelist') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
