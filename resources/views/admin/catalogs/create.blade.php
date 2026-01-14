<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Create New Catalog') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('catalogs.store') }}">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('Catalog Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full md:w-1/2"
                            required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="flex items-center mt-6">
                        <x-primary-button>{{ __('Create Catalog') }}</x-primary-button>
                        <a href="{{ route('catalogs.index') }}"
                            class="ml-4 text-sm text-gray-600 underline">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
