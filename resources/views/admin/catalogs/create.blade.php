<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('Create New Catalog Folder') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl sm:rounded-2xl border border-gray-100">

                <form action="{{ route('catalogs.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Catalog Folder Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-1">
                                    {{ __('Item Whitelist Selection') }}</h3>
                                <p class="text-xs text-gray-500">
                                    {{ __('Checked items will be visible to customers assigned to this folder.') }}</p>
                            </div>

                            <div class="w-full md:w-1/2">
                                <x-text-input id="itemSearch" type="text" class="w-full text-sm block"
                                    placeholder="🔍 Search SKU or Name..." />
                            </div>
                        </div>

                        @if ($items->isEmpty())
                            <div
                                class="p-6 bg-amber-50 rounded-xl border border-amber-100 text-amber-700 text-xs font-bold text-center uppercase">
                                {{ __('No items found. Please create items in Product Management first.') }}
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar"
                                id="itemsContainer">
                                @foreach ($items as $item)
                                    <label
                                        class="searchable-item relative flex items-center p-4 border rounded-xl hover:bg-gray-50 transition shadow-sm cursor-pointer bg-white border-gray-100">
                                        <input name="items[]" value="{{ $item->id }}" type="checkbox"
                                            class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded-lg mr-4"
                                            {{ in_array($item->id, old('items', [])) ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <div class="text-xs font-black text-blue-700 uppercase">{{ $item->sku }}
                                            </div>
                                            <div class="text-sm font-bold text-gray-800 leading-tight">
                                                {{ $item->name }}</div>
                                            <div class="text-[10px] font-mono text-gray-500 mt-1">RM
                                                {{ number_format($item->price, 2) }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end pt-8 border-t border-gray-100">
                        <a href="{{ route('catalogs.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 underline mr-6">{{ __('Cancel') }}</a>
                        <x-primary-button
                            class="bg-blue-700 hover:bg-blue-800">{{ __('Create Folder & Save Whitelist') }}</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('itemSearch');
            const items = document.querySelectorAll('.searchable-item');

            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();

                    items.forEach(item => {
                        // Searches through all text inside the label (including SKU and Name)
                        const textContent = item.textContent.toLowerCase();
                        if (textContent.includes(searchTerm)) {
                            item.style.display = ''; // Restores default flex display
                        } else {
                            item.style.display = 'none'; // Hides the item
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
