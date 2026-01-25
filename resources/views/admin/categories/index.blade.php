<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('📂 Item Categories Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Left Column: Create Form --}}
            @can('create_items')
                <div class="md:col-span-1">
                    <div class="bg-white p-6 shadow-xl sm:rounded-2xl border border-gray-100">
                        <h3 class="font-black text-sm uppercase text-gray-700 mb-4">{{ __('Create New Group') }}</h3>
                        <form action="{{ route('categories.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Category Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    required placeholder="e.g. Spare Parts" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <x-primary-button class="w-full justify-center">{{ __('Create Category') }}</x-primary-button>
                        </form>
                    </div>
                </div>
            @endcan

            {{-- Right Column: Table List --}}
            <div class="{{ auth()->user()->can('create_items') ? 'md:col-span-2' : 'md:col-span-3' }}">
                <div class="bg-white shadow-xl sm:rounded-2xl border border-gray-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-800 text-white uppercase text-[10px] tracking-widest font-black">
                                <th class="px-6 py-4">{{ __('Group Name') }}</th>
                                <th class="px-6 py-4">{{ __('Items') }}</th>
                                <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($categories as $category)
                                <tr
                                    class="transition-colors {{ $category->status === 'deactive' ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        {{ $category->name }}
                                        @if ($category->status === 'deactive')
                                            <span
                                                class="ml-2 px-2 py-0.5 bg-red-600 text-white text-[9px] font-black uppercase rounded">{{ __('Deactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black uppercase">
                                            {{ $category->items_count }} {{ __('Linked') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('categories.edit', $category) }}"
                                            class="text-indigo-600 hover:text-indigo-900 text-xs font-black uppercase border border-indigo-200 px-3 py-1 rounded-lg hover:bg-indigo-50 transition">
                                            {{ __('Manage Items') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-4 bg-gray-50 border-t">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
