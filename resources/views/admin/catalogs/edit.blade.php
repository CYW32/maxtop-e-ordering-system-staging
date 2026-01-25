<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('📂 Edit Folder: ') . $catalog->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl sm:rounded-2xl border border-gray-100">

                <form action="{{ route('catalogs.update', $catalog) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="__('Catalog Folder Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $catalog->name)" required />
                        <p class="text-[10px] text-gray-500 mt-2 italic uppercase font-bold">
                            {{ __('Changing this name affects all linked customers [Section 3.a.1].') }}</p>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-2">
                            {{ __('Item Whitelist Selection') }}</h3>
                        <p class="text-xs text-gray-500 mb-6">
                            {{ __('Checked items will be visible to customers assigned to this folder.') }}</p>

                        @if ($items->isEmpty())
                            <div
                                class="p-6 bg-amber-50 rounded-xl border border-amber-100 text-amber-700 text-xs font-bold text-center uppercase">
                                {{ __('No items found. Please create items in Product Management first.') }}
                            </div>
                        @else
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach ($items as $item)
                                    <label
                                        class="relative flex items-center p-4 border rounded-xl hover:bg-gray-50 transition shadow-sm cursor-pointer {{ in_array($item->id, $assignedItemIds) ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-100' }}">
                                        <input name="items[]" value="{{ $item->id }}" type="checkbox"
                                            class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded-lg mr-4"
                                            {{ in_array($item->id, $assignedItemIds) ? 'checked' : '' }}>
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
                            class="bg-blue-700 hover:bg-blue-800">{{ __('Save Folder & Whitelist') }}</x-primary-button>
                    </div>
                </form>

                {{-- Lifecycle Section --}}
                <div
                    class="mt-16 bg-gray-800 p-8 rounded-3xl text-white flex flex-col md:flex-row justify-between items-center gap-8 shadow-2xl">
                    <div>
                        <h4 class="font-black uppercase text-sm tracking-widest text-amber-400">
                            {{ __('Catalog Lifecycle') }}</h4>
                        <p class="text-[10px] text-gray-400 mt-2">
                            {{ __('Deactivating a folder hides ALL assigned products from linked customers immediately.') }}
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <form action="{{ route('catalogs.update', $catalog) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="name" value="{{ $catalog->name }}">

                            @if ($catalog->status === 'active')
                                <input type="hidden" name="status" value="deactive">
                                <button type="submit"
                                    class="px-6 py-2 bg-amber-500 hover:bg-amber-600 rounded-xl text-xs font-black uppercase transition shadow-md">
                                    {{ __('⛔ Deactivate Folder') }}
                                </button>
                            @else
                                <input type="hidden" name="status" value="active">
                                <button type="submit"
                                    class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded-xl text-xs font-black uppercase transition shadow-md">
                                    {{ __('✅ Reactivate Folder') }}
                                </button>
                            @endif
                        </form>

                        @if ($catalog->canBeDeleted())
                            <form action="{{ route('catalogs.destroy', $catalog) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-xl text-xs font-black uppercase transition shadow-md">
                                    {{ __('🔥 Hard Delete') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
