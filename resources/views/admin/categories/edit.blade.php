<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Edit Category') }}: <span class="text-blue-600">{{ $category->name }}</span>
            </h2>
            <a href="{{ route('categories.index') }}"
                class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back to Registry') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Back Link --}}
            <a href="{{ route('categories.index') }}"
                class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back To Product Categories') }}
            </a>

            <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                    <div
                        class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('Category Identity & Visibility') }}
                    </div>

                    <div class="space-y-8">
                        <div>
                            <x-input-label for="name" :value="__('Category Identity (Display Name)')"
                                class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <x-text-input id="name" name="name" type="text"
                                class="mt-1 block w-full font-bold uppercase" :value="old('name', $category->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Operational Status')"
                                class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <select name="status" id="status"
                                class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm font-black uppercase">
                                <option value="active" @selected(old('status', $category->status) === 'active')>
                                    {{ __('Active (Visible in Catalogs)') }}</option>
                                {{-- CHANGED 'deactive' to 'inactive' below --}}
                                <option value="inactive" @selected(old('status', $category->status) === 'inactive' || $category->status === 'deactive')>
                                    {{ __('Inactive (Hidden/Hold)') }}
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Item Assignment Section --}}
                    <div class="pt-8 mt-8 border-t border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-1">
                                    {{ __('Assign Items to Category') }}</h3>
                                <p class="text-xs text-gray-500">
                                    {{ __('Select the products that belong to this category.') }}</p>
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
                                        class="searchable-item relative flex items-center p-4 border rounded-xl hover:bg-gray-50 transition shadow-sm cursor-pointer {{ in_array($item->id, $assignedItemIds) ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-100' }}">
                                        <input name="items[]" value="{{ $item->id }}" type="checkbox"
                                            class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded-lg mr-4"
                                            {{ in_array($item->id, $assignedItemIds) ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <div class="text-xs font-black text-blue-700 uppercase">{{ $item->sku }}
                                            </div>
                                            <div class="text-sm font-bold text-gray-800 leading-tight">
                                                {{ $item->name }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-8">
                    <a href="{{ route('categories.index') }}"
                        class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button
                        class="bg-gray-900 hover:bg-black py-4 px-12 rounded-2xl shadow-lg transition-all uppercase text-[10px] font-black">
                        {{ __('Save Changes') }}
                    </x-primary-button>
                </div>

            </form>

            @if ($canBeDeleted)
                <form id="delete-category-form" action="{{ route('categories.destroy', $category) }}" method="POST"
                    class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
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
                        const textContent = item.textContent.toLowerCase();
                        item.style.display = textContent.includes(searchTerm) ? '' : 'none';
                    });
                });
            }
        });
    </script>
</x-app-layout>
