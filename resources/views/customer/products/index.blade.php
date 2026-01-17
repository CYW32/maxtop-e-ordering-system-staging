<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Available Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($items->isEmpty())
                <div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">
                    {{ __('No products are currently available in your catalog.') }}
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($items as $item)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                            <div class="p-4">
                                @if ($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}"
                                        class="w-full h-48 object-cover rounded-md mb-4">
                                @else
                                    <div
                                        class="w-full h-48 bg-gray-100 flex items-center justify-center rounded-md mb-4 text-gray-400">
                                        {{ __('No Image') }}
                                    </div>
                                @endif

                                <h3 class="text-lg font-bold text-gray-900">{{ $item->name }}</h3>
                                <p class="text-sm text-indigo-600 font-mono mb-2">{{ $item->sku }}</p>

                                <p class="text-sm text-gray-600 line-clamp-3">
                                    {{ $item->description ?? __('No description provided.') }}
                                </p>

                                <div class="mt-4">
                                    <form action="{{ route('reservation.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                                        <div class="flex items-center gap-2">
                                            <input type="number" name="quantity" value="1" min="1"
                                                class="w-20 rounded border-gray-300 text-sm">
                                            <button type="submit"
                                                class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition text-sm font-bold">
                                                {{ __('Add to Reservation') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
