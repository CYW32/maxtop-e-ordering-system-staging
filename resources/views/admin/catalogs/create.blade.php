<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Create Catalog') }}: <span class="text-blue-600">{{ __('New') }}</span>
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('catalogs.index') }}"
                    class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                    &larr; {{ __('Back To Catalogs') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Top Navigation Link (Matches Show UI) --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('catalogs.index') }}"
                    class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                    &larr; {{ __('Back To Catalogs') }}
                </a>
            </div>

            <form action="{{ route('catalogs.store') }}" method="POST" class="space-y-8">
                @csrf

                {{-- Catalog Identity Card (Matches Show UI style) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                    <div
                        class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Catalog Identity') }}
                    </div>

                    <div class="max-w-xl">
                        <x-input-label for="name" :value="__('Catalog Folder Name')"
                            class="text-[10px] font-black uppercase text-gray-400 mb-1" />
                        <x-text-input id="name" name="name" type="text"
                            class="mt-1 block w-full text-lg font-bold text-gray-900 uppercase" :value="old('name')"
                            required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                </div>

                {{-- Item Whitelist Selection Card (Matched with Edit UI) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">

                    {{-- Header, Buttons, and Filters --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div>
                            <div
                                class="text-[10px] font-black uppercase text-gray-400 tracking-widest flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                {{ __('Item Whitelist Selection') }}
                            </div>

                            {{-- Tick / Untick Buttons --}}
                            <div class="flex items-center gap-2">
                                <button type="button" id="tickAllBtn"
                                    class="text-[10px] font-bold uppercase bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg border border-blue-200 hover:bg-blue-100 transition shadow-sm">
                                    {{ __('Tick All Visible') }}
                                </button>
                                <button type="button" id="untickAllBtn"
                                    class="text-[10px] font-bold uppercase bg-gray-50 text-gray-600 px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 transition shadow-sm">
                                    {{ __('Untick All Visible') }}
                                </button>
                            </div>
                        </div>

                        {{-- Search & Category Filters --}}
                        <div class="w-full md:w-1/2 flex flex-col gap-2">
                            <x-text-input id="itemSearch" type="text" class="w-full text-sm block"
                                placeholder="🔍 Search SKU or Name..." />

                            <select id="categoryFilter"
                                class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm font-bold uppercase">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if ($items->isEmpty())
                        <div
                            class="p-8 bg-gray-50 rounded-2xl border border-gray-100 text-gray-400 text-[11px] font-bold text-center uppercase tracking-wider">
                            {{ __('No items found. Please create items first.') }}
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar"
                            id="itemsContainer">
                            @foreach ($items as $item)
                                {{-- ADDED: data-category-ids and dynamic styling to match edit view --}}
                                <label data-category-ids="{{ $item->categories->pluck('id')->implode(',') }}"
                                    class="searchable-item relative flex items-center p-4 border rounded-xl hover:bg-gray-50 transition shadow-sm cursor-pointer {{ in_array($item->id, old('items', [])) ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-100' }}">

                                    <input name="items[]" value="{{ $item->id }}" type="checkbox"
                                        class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded-lg mr-4"
                                        {{ in_array($item->id, old('items', [])) ? 'checked' : '' }}>

                                    <div class="flex-1">
                                        <div class="text-[10px] font-black text-blue-500 uppercase tracking-tighter">
                                            {{ $item->sku }}
                                        </div>
                                        <div class="text-sm font-bold text-gray-800 leading-tight mt-0.5">
                                            {{ $item->name }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-6 px-4">
                    <a href="{{ route('catalogs.index') }}"
                        class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-[2rem] text-[11px] font-black uppercase tracking-[0.1em] transition-all shadow-lg shadow-blue-100">
                        {{ __('Create Folder & Save Whitelist') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Ported JS from Edit View --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('itemSearch');
            const categoryFilter = document.getElementById('categoryFilter');
            const tickAllBtn = document.getElementById('tickAllBtn');
            const untickAllBtn = document.getElementById('untickAllBtn');
            const items = document.querySelectorAll('.searchable-item');

            // 1. Filter Logic
            function filterItems() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                const selectedCategory = categoryFilter ? categoryFilter.value : '';

                items.forEach(item => {
                    const textContent = item.textContent.toLowerCase();
                    const itemCategoryIds = item.getAttribute('data-category-ids').split(',');

                    const matchesSearch = textContent.includes(searchTerm);
                    const matchesCategory = selectedCategory === '' || itemCategoryIds.includes(
                        selectedCategory);

                    item.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterItems);
            }
            if (categoryFilter) {
                categoryFilter.addEventListener('change', filterItems);
            }

            // 2. Auto-Tick All Logic when Category is Selected
            if (categoryFilter) {
                categoryFilter.addEventListener('change', function() {
                    const selectedCategory = this.value;

                    if (selectedCategory !== '') {
                        items.forEach(item => {
                            const itemCategoryIds = item.getAttribute('data-category-ids').split(
                                ',');
                            const checkbox = item.querySelector('input[type="checkbox"]');

                            if (itemCategoryIds.includes(selectedCategory) && checkbox) {
                                checkbox.checked = true;
                                item.classList.add('bg-blue-50', 'border-blue-200');
                                item.classList.remove('bg-white', 'border-gray-100');
                            }
                        });
                    }
                });
            }

            // 3. Keep visual styling updated if user manually clicks a checkbox
            items.forEach(item => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            item.classList.add('bg-blue-50', 'border-blue-200');
                            item.classList.remove('bg-white', 'border-gray-100');
                        } else {
                            item.classList.remove('bg-blue-50', 'border-blue-200');
                            item.classList.add('bg-white', 'border-gray-100');
                        }
                    });
                }
            });

            // 4. Tick / Untick All Visible Logic
            if (tickAllBtn) {
                tickAllBtn.addEventListener('click', function() {
                    items.forEach(item => {
                        // Only interact with items that are currently visible (filtered)
                        if (item.style.display !== 'none') {
                            const checkbox = item.querySelector('input[type="checkbox"]');
                            if (checkbox && !checkbox.checked) {
                                checkbox.checked = true;
                                item.classList.add('bg-blue-50', 'border-blue-200');
                                item.classList.remove('bg-white', 'border-gray-100');
                            }
                        }
                    });
                });
            }

            if (untickAllBtn) {
                untickAllBtn.addEventListener('click', function() {
                    items.forEach(item => {
                        // Only interact with items that are currently visible (filtered)
                        if (item.style.display !== 'none') {
                            const checkbox = item.querySelector('input[type="checkbox"]');
                            if (checkbox && checkbox.checked) {
                                checkbox.checked = false;
                                item.classList.remove('bg-blue-50', 'border-blue-200');
                                item.classList.add('bg-white', 'border-gray-100');
                            }
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
