<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Company Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- SUCCESS NOTIFICATION ALERT --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show"
                    class="bg-green-50 border border-green-200 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm transition-all">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span
                            class="text-xs font-black uppercase text-green-800 tracking-wide">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="p-1 text-green-400 hover:text-green-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- ERROR NOTIFICATION ALERT --}}
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show"
                    class="bg-red-50 border border-red-200 p-4 rounded-2xl flex items-center justify-between gap-3 shadow-sm transition-all">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span
                            class="text-xs font-black uppercase text-red-800 tracking-wide">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="p-1 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- 1. CREATE BUTTON (TOP) --}}
            <div class="mb-6">
                <a href="{{ route('companys.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase transition-all shadow-lg shadow-blue-100 inline-block mt-2">
                    {{ __('+ Register New Business') }}
                </a>
            </div>

            {{-- 2. SEARCH TOOLBAR (MIDDLE) --}}
            <div class="bg-white p-4 md:p-6 rounded-[2rem] border border-gray-100 shadow-sm mb-6"
                x-data="{ showFilters: {{ request()->hasAny(['status', 'type', 'hq_id']) ? 'true' : 'false' }} }">
                <form method="GET" action="{{ route('companys.index') }}" class="flex flex-col gap-4">

                    {{-- Top Row: Text Search & Main Actions --}}
                    <div class="flex flex-col md:flex-row items-center gap-3 w-full">
                        <div class="relative flex-1 w-full">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Search name, code, or branch code...') }}"
                                class="w-full pl-12 pr-4 py-3 rounded-2xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-shadow shadow-sm" />
                            <svg class="w-5 h-5 text-gray-400 absolute left-5 top-3.5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                            <button type="submit"
                                class="w-full md:w-auto bg-gray-900 hover:bg-black text-white px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all shadow-md whitespace-nowrap">
                                {{ __('Search') }}
                            </button>

                            <button type="button" @click="showFilters = !showFilters"
                                class="w-full md:w-auto flex items-center justify-center bg-white border border-gray-200 text-gray-600 hover:text-gray-900 px-5 py-3 rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all shadow-sm hover:bg-gray-50 whitespace-nowrap">
                                <span
                                    x-text="showFilters ? '{{ __('Hide Filters') }}' : '{{ __('More Filters') }}'"></span>
                                <svg class="w-4 h-4 ml-2 transition-transform duration-200"
                                    :class="showFilters ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            @if (request()->hasAny(['search', 'status', 'type', 'hq_id']))
                                <a href="{{ route('companys.index') }}"
                                    class="flex items-center justify-center bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 px-4 py-3 rounded-2xl transition-all shadow-sm border border-gray-200 hover:border-red-200"
                                    title="{{ __('Clear All Filters') }}">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Bottom Row: Advanced Filters --}}
                    <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" style="display: none;"
                        class="pt-5 mt-2 border-t border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">

                            {{-- Type Filter (All Types REMOVED) --}}
                            <div class="w-full">
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Company Type') }}</label>
                                <select name="type"
                                    class="w-full py-3 px-4 rounded-2xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm font-bold text-gray-600 shadow-sm transition-shadow bg-white cursor-pointer"
                                    @change="$el.form.submit()">
                                    {{-- If nothing is requested, or if 'hq' is requested, select HQ --}}
                                    <option value="hq" {{ request('type') !== 'branch' ? 'selected' : '' }}>
                                        {{ __('HQ Only') }}</option>
                                    <option value="branch" {{ request('type') === 'branch' ? 'selected' : '' }}>
                                        {{ __('Branch Only') }}</option>
                                </select>
                            </div>

                            {{-- Status Filter --}}
                            <div class="w-full">
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Operational Status') }}</label>
                                <select name="status"
                                    class="w-full py-3 px-4 rounded-2xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm font-bold text-gray-600 shadow-sm transition-shadow bg-white cursor-pointer"
                                    @change="$el.form.submit()">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                        {{ __('Active') }}</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                        {{ __('Inactive') }}</option>
                                </select>
                            </div>

                            {{-- HQ Specific Filter --}}
                            <div class="w-full">
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('Under Headquarters') }}</label>
                                <select name="hq_id"
                                    class="w-full py-3 px-4 rounded-2xl border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm font-bold text-gray-600 shadow-sm transition-shadow bg-white cursor-pointer"
                                    @change="$el.form.submit()">
                                    <option value="">{{ __('All Headquarters') }}</option>
                                    @foreach ($hqs as $hq)
                                        <option value="{{ $hq->id }}"
                                            {{ request('hq_id') == $hq->id ? 'selected' : '' }}>
                                            {{ $hq->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            {{-- 3. DATA TABLE (BOTTOM) --}}
            <div class="bg-white shadow-sm sm:rounded-[2.5rem] border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Business Entity') }}</th>
                            <th
                                class="px-6 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Catalog / Location') }}</th>
                            <th
                                class="px-6 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Status') }}</th>
                            <th
                                class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Management') }}</th>
                        </tr>
                    </thead>

                    @if ($isFlatView)
                        {{-- ========================================== --}}
                        {{-- FLAT VIEW (Used when searching/filtering)  --}}
                        {{-- ========================================== --}}
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse ($companys as $company)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            @if (is_null($company->parent_id))
                                                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white text-[10px] font-black shadow-sm"
                                                    title="Headquarters">HQ</div>
                                            @else
                                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 text-[10px] font-black border border-gray-200"
                                                    title="Branch">BR</div>
                                            @endif
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $company->company_name }}</span>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span
                                                        class="text-[10px] font-mono text-gray-500 uppercase font-bold">{{ $company->company_code ?? $company->branch_code }}</span>
                                                    @if ($company->parent_id)
                                                        <span class="text-gray-300">|</span>
                                                        <span
                                                            class="text-[9px] font-black text-blue-500 uppercase tracking-wider">{{ __('Under HQ:') }}
                                                            {{ $company->parent->company_name ?? __('Unknown') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5 items-start">
                                            @if ($company->catalog_id)
                                                <span
                                                    class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-blue-50 text-blue-700 border border-blue-100">{{ $company->catalog->name }}</span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-gray-50 text-gray-500 border border-gray-200">{{ __('Inherited / None') }}</span>
                                            @endif
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $company->city ?: 'N/A' }}, {{ $company->state ?: 'N/A' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <span
                                            class="px-3 py-1 rounded-full text-[9px] font-black uppercase border shadow-sm {{ ($company->status ?? 'active') === 'active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                            {{ $company->status ?? 'Active' }}
                                        </span>
                                    </td>

                                    <td class="px-8 py-5 text-right">
                                        <div class="flex justify-end gap-2">
                                            @if (is_null($company->parent_id))
                                                <a href="{{ route('companys.create', ['parent_id' => $company->id]) }}"
                                                    class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-green-600 hover:border-green-100 transition shadow-sm"
                                                    title="{{ __('Add Branch') }}">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </a>
                                            @endif

                                            <a href="{{ route('companys.show', $company) }}"
                                                class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-green-600 hover:border-green-100 transition shadow-sm"
                                                title="{{ __('View') }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('companys.edit', $company) }}"
                                                class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-blue-600 hover:border-blue-100 transition shadow-sm"
                                                title="{{ __('Edit') }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>

                                            <form action="{{ route('companys.destroy', $company) }}" method="POST"
                                                class="inline-block m-0 p-0"
                                                onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this company? This action cannot be undone.') }}');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-red-600 hover:border-red-100 hover:bg-red-50 transition shadow-sm"
                                                    title="{{ __('Delete') }}">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100" class="px-8 py-20 text-center bg-white"><span
                                            class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em]">{{ __('No Companies Found') }}</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    @else
                        {{-- ========================================== --}}
                        {{-- ACCORDION VIEW (Default Hierarchical)      --}}
                        {{-- ========================================== --}}
                        @forelse ($companys as $company)
                            <tbody x-data="{ expanded: false }"
                                class="bg-white border-b border-gray-100 last:border-0 transition-all">

                                {{-- 1. HEADQUARTER ROW --}}
                                <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer group"
                                    @click="expanded = !expanded">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">

                                            {{-- Dynamic Arrow Toggle Button --}}
                                            <div class="w-6 flex justify-center items-center">
                                                @if ($company->children->count() > 0)
                                                    <div
                                                        class="w-6 h-6 rounded-full flex items-center justify-center bg-gray-50 text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-all border border-gray-200 group-hover:border-blue-200">
                                                        <svg class="w-3.5 h-3.5 transition-transform duration-300"
                                                            :class="expanded ? 'rotate-180' : ''" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="3">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white text-[10px] font-black shadow-sm"
                                                title="Headquarters">HQ</div>

                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $company->company_name }}</span>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span
                                                        class="text-[10px] font-mono text-gray-500 uppercase font-bold">{{ $company->company_code }}</span>
                                                    @if ($company->children->count() > 0)
                                                        <span class="text-gray-300">|</span>
                                                        <span
                                                            class="text-[9px] font-black text-blue-500 uppercase tracking-wider"
                                                            x-text="expanded ? '{{ __('Hide Branches') }}' : '{{ $company->children->count() }} {{ __('Branches') }}'"></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Catalog & Location --}}
                                    <td class="px-6 py-5" @click.stop>
                                        <div class="flex flex-col gap-1.5 items-start">
                                            @if ($company->catalog_id)
                                                <span
                                                    class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-blue-50 text-blue-700 border border-blue-100">{{ $company->catalog->name }}</span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-gray-50 text-gray-500 border border-gray-200">{{ __('Inherited / None') }}</span>
                                            @endif
                                            <span
                                                class="text-[10px] font-bold text-gray-400 uppercase flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $company->city ?: 'N/A' }}, {{ $company->state ?: 'N/A' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-5 text-center" @click.stop>
                                        <span
                                            class="px-3 py-1 rounded-full text-[9px] font-black uppercase border shadow-sm {{ ($company->status ?? 'active') === 'active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                            {{ $company->status ?? 'Active' }}
                                        </span>
                                    </td>

                                    {{-- Management Buttons --}}
                                    <td class="px-8 py-5 text-right" @click.stop>
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('companys.create', ['parent_id' => $company->id]) }}"
                                                class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-green-600 hover:border-green-100 transition shadow-sm"
                                                title="{{ __('Add Branch') }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('companys.show', $company) }}"
                                                class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-green-600 hover:border-green-100 transition shadow-sm"
                                                title="{{ __('View') }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('companys.edit', $company) }}"
                                                class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-blue-600 hover:border-blue-100 transition shadow-sm"
                                                title="{{ __('Edit') }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>

                                            <form action="{{ route('companys.destroy', $company) }}" method="POST"
                                                class="inline-block m-0 p-0"
                                                onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this company? This action cannot be undone.') }}');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-red-600 hover:border-red-100 hover:bg-red-50 transition shadow-sm"
                                                    title="{{ __('Delete') }}">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- 2. BRANCH ROWS (Hidden by default, shown on click) --}}
                                @foreach ($company->children as $branch)
                                    <tr x-show="expanded" style="display: none;"
                                        class="bg-gray-50/60 hover:bg-gray-100/50 border-t border-gray-100 transition-colors">
                                        <td class="px-8 py-4">
                                            {{-- Indented Left --}}
                                            <div class="flex items-center gap-3 pl-[3.25rem]">
                                                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-gray-400 text-[10px] font-black border border-gray-200 shadow-sm"
                                                    title="Branch">BR</div>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[13px] font-black text-gray-700 uppercase tracking-tight">{{ $branch->company_name }}</span>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <span
                                                            class="text-[10px] font-mono text-gray-400 uppercase font-bold">{{ $branch->branch_code }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1.5 items-start opacity-90">
                                                @if ($branch->catalog_id)
                                                    <span
                                                        class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-blue-50 text-blue-700 border border-blue-100">{{ $branch->catalog->name }}</span>
                                                @else
                                                    <span
                                                        class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-gray-50 text-gray-500 border border-gray-200">{{ __('Inherited / None') }}</span>
                                                @endif
                                                <span
                                                    class="text-[10px] font-bold text-gray-400 uppercase flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    {{ $branch->city ?: 'N/A' }}, {{ $branch->state ?: 'N/A' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase border shadow-sm {{ ($branch->status ?? 'active') === 'active' ? 'bg-green-50/50 text-green-700 border-green-200' : 'bg-red-50/50 text-red-700 border-red-200' }}">
                                                {{ $branch->status ?? 'Active' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-4 text-right">
                                            <div
                                                class="flex justify-end gap-2 opacity-80 hover:opacity-100 transition-opacity">
                                                <a href="{{ route('companys.show', $branch) }}"
                                                    class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-green-600 hover:border-green-100 transition shadow-sm"
                                                    title="{{ __('View') }}">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                <a href="{{ route('companys.edit', $branch) }}"
                                                    class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-blue-600 hover:border-blue-100 transition shadow-sm"
                                                    title="{{ __('Edit') }}">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2.5">
                                                        <path
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </a>

                                                <form action="{{ route('companys.destroy', $branch) }}"
                                                    method="POST" class="inline-block m-0 p-0"
                                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this branch?') }}');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-red-600 hover:border-red-100 hover:bg-red-50 transition shadow-sm"
                                                        title="{{ __('Delete') }}">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @empty
                            <tbody>
                                <tr>
                                    <td colspan="100" class="px-8 py-20 text-center bg-white"><span
                                            class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em]">{{ __('No Companies Found') }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        @endforelse
                    @endif
                </table>
            </div>

            <div class="mt-6">{{ $companys->links() }}</div>
        </div>
    </div>
</x-app-layout>
