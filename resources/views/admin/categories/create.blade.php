<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Initialize New Category') }}
            </h2>
            <a href="{{ route('categories.index') }}" class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back to Registry') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('categories.store') }}" class="space-y-8">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                    <div class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        {{ __('Category Definition') }}
                    </div>

                    <div class="space-y-8">
                        <div>
                            <x-input-label for="name" :value="__('Category Identity (Display Name)')" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full font-bold uppercase" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Initial Operational Status')" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <select name="status" id="status" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm font-black uppercase">
                                <option value="active" @selected(old('status') === 'active')>{{ __('Active (Immediately Available for Catalogs)') }}</option>
                                <option value="deactive" @selected(old('status') === 'deactive')>{{ __('Inactive (System Hold)') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    {{-- CANCEL: Redirects to index [User Request] --}}
                    <a href="{{ route('categories.index') }}" class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                        {{ __('Cancel') }}
                    </a>
                    {{-- CREATE: Redirects to index with success message [User Request] --}}
                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 py-4 px-12 rounded-2xl shadow-lg shadow-blue-100 transition-all uppercase text-[10px] font-black">
                        {{ __('Create Category') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
