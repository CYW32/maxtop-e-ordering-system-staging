<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Edit Category') }}: <span class="text-blue-600">{{ $category->name }}</span>
            </h2>
            <a href="{{ route('categories.index') }}" class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                &larr; {{ __('Back to Registry') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                    <div class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        {{ __('Category Identity & Visibility') }}
                    </div>

                    <div class="space-y-8">
                        {{-- CATEGORY NAME --}}
                        <div>
                            <x-input-label for="name" :value="__('Category Identity (Display Name)')" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full font-bold uppercase" :value="old('name', $category->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        {{-- OPERATIONAL STATUS --}}
                        <div>
                            <x-input-label for="status" :value="__('Operational Status')" class="text-[10px] font-black uppercase text-gray-400 mb-2" />
                            <select name="status" id="status" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 text-sm font-black uppercase">
                                <option value="active" @selected(old('status', $category->status) === 'active')>{{ __('Active (Visible in Catalogs)') }}</option>
                                <option value="deactive" @selected(old('status', $category->status) === 'deactive')>{{ __('Inactive (Hidden/Hold)') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    {{-- ARCHITECTURE CHECK: Only show Hard Delete if no transaction records exist [User Request] --}}
                    @if($canBeDeleted)
                        <button type="button" 
                                onclick="if(confirm('{{ __('WARNING: This will permanently purge this category. This action is irreversible. Proceed?') }}')) document.getElementById('delete-category-form').submit();"
                                class="text-[10px] font-black uppercase text-red-400 hover:text-red-600 transition tracking-tighter">
                            {{ __('Hard Delete') }}
                        </button>
                    @else
                        <span class="text-[9px] font-black uppercase text-gray-300 italic flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            {{ __('Deletion Locked: Transaction Records Exist') }} [9.c.1]
                        </span>
                    @endif

                    <div class="flex items-center gap-4">
                        <a href="{{ route('categories.index') }}" class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button class="bg-gray-900 hover:bg-black py-4 px-12 rounded-2xl shadow-lg transition-all uppercase text-[10px] font-black">
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>

            {{-- HIDDEN DELETE FORM --}}
            @if($canBeDeleted)
                <form id="delete-category-form" action="{{ route('categories.destroy', $category) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
