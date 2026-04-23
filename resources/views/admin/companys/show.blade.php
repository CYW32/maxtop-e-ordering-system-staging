<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('View Business Entity') }}: <span class="text-blue-600">{{ $company->company_name }}</span>
            </h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('companys.index') }}"
                    class="text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                    &larr; {{ __('Back To List') }}
                </a>

                {{-- EDIT BUTTON (Permission check removed so it is always visible) --}}
                <a href="{{ route('companys.edit', $company) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-2xl text-[10px] font-black uppercase transition-all shadow-lg shadow-blue-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- TOP RIGHT ACTION BUTTONS --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('companys.index') }}"
                    class="text-[11px] font-black uppercase text-gray-400 hover:text-gray-600 transition tracking-widest">
                    &larr; {{ __('Back To Business Directory') }}
                </a>

                {{-- SECOND EDIT BUTTON (Permission check removed) --}}
                <a href="{{ route('companys.edit', $company) }}"
                    class="bg-gray-900 hover:bg-black text-white py-3.5 px-8 rounded-[2rem] shadow-xl shadow-gray-200 transition-all uppercase text-[11px] font-black tracking-[0.1em] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    {{ __('Edit Entity') }}
                </a>
            </div>

            {{-- 1. Business Identity Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                <div
                    class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ __('Entity Identity & Status') }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-1">{{ __('Business Name') }}</h3>
                        <div class="flex items-center gap-3 mt-1">
                            @if (is_null($company->parent_id))
                                <div
                                    class="w-7 h-7 bg-blue-600 rounded flex items-center justify-center text-white text-[9px] font-black shadow-sm">
                                    HQ</div>
                            @else
                                <div
                                    class="w-7 h-7 bg-gray-100 rounded flex items-center justify-center text-gray-500 text-[9px] font-black border border-gray-200">
                                    BR</div>
                            @endif
                            <p class="text-lg font-bold text-gray-900 uppercase">{{ $company->company_name }}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-2">{{ __('Operational Status') }}
                        </h3>
                        <span
                            class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase border shadow-sm {{ ($company->status ?? 'active') === 'active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                            {{ $company->status ?? 'Active' }}
                        </span>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-1">{{ __('Entity Code') }}</h3>
                        <p class="text-sm font-bold text-gray-900 uppercase font-mono">
                            {{ $company->company_code ?? ($company->branch_code ?? 'N/A') }}</p>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-1">
                            {{ __('Registration No. (SSM)') }}</h3>
                        <p class="text-sm font-bold text-gray-900 uppercase">{{ $company->company_reg_no ?: 'N/A' }}
                        </p>
                    </div>

                    <div class="md:col-span-2 pt-6 border-t border-gray-50">
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-1">
                            {{ __('Assigned Catalog (Whitelist)') }}</h3>
                        @if ($company->catalog)
                            <span
                                class="inline-block mt-1 px-3 py-1 rounded-lg text-[10px] font-black uppercase bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $company->catalog->name }}
                            </span>
                        @else
                            <p class="text-sm font-bold text-gray-400 italic mt-1">
                                {{ __('Inherited or None Assigned') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. Contact & Location Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                <div
                    class="text-[10px] font-black uppercase text-gray-400 mb-8 tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ __('Contact & Location') }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-1">
                            {{ __('Person In Charge (PIC)') }}</h3>
                        <p class="text-sm font-bold text-gray-900 uppercase">{{ $company->pic_name ?: 'N/A' }}</p>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-1">{{ __('PIC Phone Number') }}
                        </h3>
                        <p class="text-sm font-bold text-gray-900 uppercase font-mono">
                            {{ $company->pic_phone ?: 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-1">{{ __('Delivery Address') }}
                        </h3>
                        <p class="text-sm font-bold text-gray-900 uppercase leading-relaxed">
                            {{ $company->delivery_address ?: 'N/A' }}</p>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-1">{{ __('City') }}</h3>
                        <p class="text-sm font-bold text-gray-900 uppercase">{{ $company->city ?: 'N/A' }}</p>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase text-gray-400 mb-1">{{ __('State & Postal Code') }}
                        </h3>
                        <p class="text-sm font-bold text-gray-900 uppercase">{{ $company->state ?: 'N/A' }}
                            {{ $company->postal_code ? '(' . $company->postal_code . ')' : '' }}</p>
                    </div>
                </div>
            </div>

            {{-- 3. Hierarchy / Network Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100 p-10">
                <div
                    class="text-[10px] font-black uppercase text-gray-400 mb-6 tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    {{ __('Business Network Hierarchy') }}
                </div>

                @if ($company->parent_id)
                    {{-- IT IS A BRANCH --}}
                    <div>
                        <p class="text-[11px] font-bold text-gray-500 uppercase mb-3">
                            {{ __('This entity is a Branch operating under:') }}</p>
                        <a href="{{ route('companys.show', $company->parent_id) }}"
                            class="flex items-center p-4 border border-blue-100 rounded-2xl bg-blue-50/50 hover:bg-blue-50 transition-colors">
                            <div
                                class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white text-[10px] font-black shadow-sm mr-4 shrink-0">
                                HQ</div>
                            <div>
                                <div class="text-[10px] font-black text-blue-500 uppercase tracking-tighter">
                                    {{ $company->parent->company_code ?? 'N/A' }}</div>
                                <div class="text-sm font-bold text-gray-900 leading-tight mt-0.5">
                                    {{ $company->parent->company_name ?? 'Unknown HQ' }}</div>
                            </div>
                        </a>
                    </div>
                @else
                    {{-- IT IS AN HQ --}}
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-[11px] font-bold text-gray-500 uppercase">
                                {{ __('Registered Branches under this HQ:') }} <span
                                    class="text-blue-600 font-black">({{ $company->children->count() }})</span></p>

                            {{-- Add Branch Button (Permission check removed so it is always visible) --}}
                            <a href="{{ route('companys.create', ['parent_id' => $company->id]) }}"
                                class="text-[10px] font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest">+
                                {{ __('Add Branch') }}</a>
                        </div>

                        @if ($company->children->isEmpty())
                            <div
                                class="p-6 bg-gray-50 rounded-2xl border border-gray-100 text-gray-400 text-[11px] font-bold text-center uppercase tracking-wider">
                                {{ __('No branches are currently assigned to this HQ.') }}
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($company->children as $branch)
                                    <a href="{{ route('companys.show', $branch) }}"
                                        class="flex items-center p-4 border border-gray-200 rounded-2xl bg-white hover:border-blue-300 hover:shadow-md transition-all group">
                                        <div
                                            class="w-8 h-8 bg-gray-100 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors rounded-lg flex items-center justify-center text-gray-500 text-[9px] font-black border border-gray-200 mr-3 shrink-0">
                                            BR</div>
                                        <div class="flex-1 truncate">
                                            <div
                                                class="text-[10px] font-black text-gray-400 group-hover:text-blue-400 uppercase tracking-tighter transition-colors">
                                                {{ $branch->branch_code }}</div>
                                            <div class="text-xs font-bold text-gray-800 truncate mt-0.5">
                                                {{ $branch->company_name }}</div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
