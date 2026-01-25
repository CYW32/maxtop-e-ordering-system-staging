<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('⚙️ Manage Group:') }} {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 shadow-xl sm:rounded-2xl border border-gray-100">

                <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="__('Group Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $category->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4">
                            {{ __('Select Items for this Group') }}</h3>

                        @if ($items->isEmpty())
                            <p class="text-xs text-gray-500 italic uppercase">
                                {{ __('No items available. Create items in Product Management first.') }}</p>
                        @else
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach ($items as $item)
                                    <label
                                        class="relative flex items-center p-3 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer {{ in_array($item->id, $assignedItemIds) ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-200' }}">
                                        <input name="items[]" value="{{ $item->id }}" type="checkbox"
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            {{ in_array($item->id, $assignedItemIds) ? 'checked' : '' }}>
                                        <div class="ml-3">
                                            <div class="text-sm font-bold text-gray-800 leading-tight">
                                                {{ $item->name }}</div>
                                            <div class="text-[10px] font-black text-blue-600 uppercase">
                                                {{ $item->sku }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                        <a href="{{ route('categories.index') }}"
                            class="text-sm text-gray-600 underline mr-4">{{ __('Cancel') }}</a>
                        <x-primary-button
                            class="bg-blue-700 hover:bg-blue-800">{{ __('Save Changes') }}</x-primary-button>
                    </div>
                </form>

                {{-- Lifecycle Section: Fulfills Soft Deactivation & Secure Delete Requirements --}}
                <div
                    class="mt-12 bg-gray-50 p-6 rounded-2xl border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <h4 class="font-black uppercase text-xs text-gray-700">
                            {{ __('Category Visibility & Lifecycle') }}</h4>
                        <p class="text-[10px] text-gray-500 italic mt-1">
                            {{ __('Deactivated categories are hidden from the customer catalog.') }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Visibility Toggle --}}
                        <form action="{{ route('categories.update', $category) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="name" value="{{ $category->name }}">
                            <input type="hidden" name="status"
                                value="{{ $category->status === 'active' ? 'deactive' : 'active' }}">

                            <button type="submit"
                                class="px-6 py-2 rounded-xl text-xs font-black uppercase transition shadow-md {{ $category->status === 'active' ? 'bg-amber-500 hover:bg-amber-600 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                                {{ $category->status === 'active' ? __('⛔ Deactivate') : __('✅ Reactivate') }}
                            </button>
                        </form>

                        {{-- Hard Delete Guard --}}
                        @if ($category->canBeDeleted())
                            <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                onsubmit="return confirm('Hard delete this category? Items will remain but the grouping will be lost.');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-xl text-xs font-black uppercase transition shadow-md">
                                    {{ __('🔥 Hard Delete') }}
                                </button>
                            </form>
                        @else
                            <div class="bg-white text-gray-400 px-4 py-2 rounded-xl text-[9px] font-black uppercase border border-gray-200 cursor-help"
                                title="Locked: Items in this category have order history.">
                                {{ __('🔒 Deletion Locked') }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
