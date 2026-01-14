<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Item Master List') }}
            </h2>

            {{-- <!-- Requirement: Show Create btn ONLY if View AND Create are ticked -->
            @if (auth()->user()->can('view_items') && auth()->user()->can('create_items'))
                <a href="{{ route('items.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    + {{ __('Create New Item') }}
                </a>
            @endif --}}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @can('create_items')
                <div class="flex justify-end mb-4">
                    <a href="{{ route('items.create') }}"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                        + {{ __('Create New Item') }}
                    </a>
                </div>
            @endcan

            <!-- Search Toolbar -->
            <div class="mb-6">
                <form method="GET" action="{{ route('items.index') }}" class="flex gap-4">
                    <x-text-input name="search" placeholder="Search SKU or Name..." class="w-full" :value="request('search')" />
                    <x-primary-button>{{ __('Filter') }}</x-primary-button>
                    @if (request('search'))
                        <a href="{{ route('items.index') }}"
                            class="bg-gray-200 px-4 py-2 rounded flex items-center text-sm">Clear</a>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('Image') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('SKU / Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('Price') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                {{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($item->image_path)
                                        <img src="{{ asset('storage/' . $item->image_path) }}"
                                            class="h-12 w-12 object-cover rounded border">
                                    @else
                                        <div
                                            class="h-12 w-12 bg-gray-100 flex items-center justify-center text-xs text-gray-400">
                                            No Image</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-indigo-600">{{ $item->sku }}</div>
                                    <div class="text-sm text-gray-900">{{ $item->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    RM {{ number_format($item->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <!-- Requirement: Show Edit ONLY if View, Create, AND Edit are ticked -->
                                    @if (auth()->user()->can('view_items') && auth()->user()->can('create_items') && auth()->user()->can('edit_items'))
                                        <a href="{{ route('items.edit', $item->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 mr-4">
                                            {{ __('Edit') }}
                                        </a>
                                    @endif

                                    {{-- @role('admin')
                                        <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this item?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Delete') }}</button>
                                        </form>
                                    @endrole --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">{{ $items->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
