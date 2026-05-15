<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Edit Catalog') }}: <span class="text-blue-600">{{ $catalog->name }}</span>
            </h2>
            <a href="{{ route('catalogs.index') }}"
                class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back to Catalog Folders') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Back Link --}}
            <a href="{{ route('catalogs.index') }}"
                class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back To Catalog Folders') }}
            </a>

            <form method="POST" action="{{ route('catalogs.update', $catalog) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                    <div
                        class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        {{ __('Catalog Identity & Visibility') }}
                    </div>

                    <div class="space-y-8">
                        <div>
                            <x-input-label for="name" :value="__('Catalog Folder Name (Display Name)')"
                                class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <x-text-input id="name" name="name" type="text"
                                class="mt-1 block w-full font-bold uppercase" :value="old('name', $catalog->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            <p class="text-[9px] text-gray-400 mt-2 uppercase font-bold tracking-wider">
                                {{ __('Changing this name affects all linked customers.') }}</p>
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Operational Status')"
                                class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <select name="status" id="status"
                                class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm font-black uppercase">
                                <option value="active" @selected(old('status', $catalog->status) === 'active')>
                                    {{ __('Active (Visible to Customers)') }}</option>

                                {{-- CHANGED: deactive to inactive here --}}
                                <option value="inactive" @selected(old('status', $catalog->status) === 'inactive')>
                                    {{ __('Inactive (Hidden from Customers)') }}
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Item Whitelist Section --}}
                    <div class="pt-8 mt-8 border-t border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-1">
                                    {{ __('Item Whitelist Selection') }}</h3>
                                <p class="text-xs text-gray-500">
                                    {{ __('Checked products will be visible to customers assigned to this catalog.') }}
                                </p>

                                <div class="flex items-center gap-2 mt-3">
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

                            <div class="w-full md:w-1/2 flex flex-col gap-2">
                                <x-text-input id="itemSearch" type="text" class="w-full text-sm block"
                                    placeholder="🔍 Search SKU or Name..." />

                                {{-- ADDED: Category Dropdown Filter --}}
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
                                class="p-6 bg-amber-50 rounded-xl border border-amber-100 text-amber-700 text-xs font-bold text-center uppercase">
                                {{ __('No items found. Please create items in Product Management first.') }}
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar"
                                id="itemsContainer">
                                @foreach ($items as $item)
                                    {{-- ADDED: data-category-id --}}
                                    <label data-category-ids="{{ $item->categories->pluck('id')->implode(',') }}"
                                        class="searchable-item relative flex items-center p-4 border rounded-xl hover:bg-gray-50 transition shadow-sm cursor-pointer {{ in_array($item->id, $assignedItemIds) ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-100' }}">
                                        <input name="items[]" value="{{ $item->id }}" type="checkbox"
                                            class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded-lg mr-4"
                                            {{ in_array($item->id, $assignedItemIds) ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <div class="text-xs font-black text-blue-700 uppercase">
                                                    {{ $item->sku }}</div>

                                                {{-- AMEND HERE: Added 'hidden' class to hide the price --}}
                                                <div
                                                    class="hidden text-[10px] font-mono text-gray-500 font-bold bg-white px-2 py-0.5 rounded shadow-sm border border-gray-100">
                                                    RM {{ number_format($item->price, 2) }}</div>
                                            </div>
                                            <div class="text-sm font-bold text-gray-800 leading-tight mt-1">
                                                {{ $item->name }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-8">
                    <a href="{{ route('catalogs.index') }}"
                        class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button
                        class="bg-gray-900 hover:bg-black py-4 px-12 rounded-2xl shadow-lg transition-all uppercase text-[10px] font-black">
                        {{ __('Save Folder & Whitelist') }}
                    </x-primary-button>
                </div>

            </form>

            {{-- Danger Zone: Hard Delete --}}
            @if ($catalog->canBeDeleted())
                <div class="mt-12 flex justify-center">
                    <form action="{{ route('catalogs.destroy', $catalog) }}" method="POST"
                        onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this catalog? This action cannot be undone.') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-[10px] font-black uppercase text-red-400 hover:text-red-600 transition tracking-widest border-b border-transparent hover:border-red-600 pb-0.5">
                            {{ __('Delete Catalog Permanently') }}
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>

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
                    // Split the comma separated string into an array
                    const itemCategoryIds = item.getAttribute('data-category-ids').split(',');

                    const matchesSearch = textContent.includes(searchTerm);
                    // Check if the array includes the selected category
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
                            // Split the comma separated string into an array
                            const itemCategoryIds = item.getAttribute('data-category-ids').split(
                                ',');
                            const checkbox = item.querySelector('input[type="checkbox"]');

                            // Check if the array includes the selected category
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
