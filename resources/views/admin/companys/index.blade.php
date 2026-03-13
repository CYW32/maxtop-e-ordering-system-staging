<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Business Entities') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mb-4">
            <x-filter-toolbar
                :placeholder="__('Search Company Code, Name or Email...')"
                :search="request('search')"
                :action="route('companys.index')"
            />
        </div>
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('companys.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-2xl text-xs font-black uppercase shadow-lg shadow-blue-200 transition-all">
                {{ __('Register New Business') }}
            </a>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Business Name / Code') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Assigned Catalog') }}</th>
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Logistics') }}</th>
                            <th
                                class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @foreach ($companys as $hq)
                            {{-- HQ Row --}}
                            <tr class="bg-blue-50/30">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white text-xs font-black">
                                            HQ</div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900">{{ $hq->company_name }}</span>
                                            <span
                                                class="text-[10px] font-mono text-blue-500 uppercase font-black">{{ $hq->company_code }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full bg-white border border-blue-200 text-blue-700 text-[9px] font-black uppercase">
                                        {{ $hq->catalog->name ?? __('No Catalog') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 truncate max-w-xs">
                                    {{ $hq->delivery_address }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3">
                                        {{-- Fulfills requirement: Create branch directly for this HQ --}}
                                        <a href="{{ route('companys.create', ['parent_id' => $hq->id]) }}"
                                            class="text-[10px] font-black uppercase text-green-600 hover:underline">
                                            {{ __('Add Branch') }}
                                        </a>
                                        <a href="{{ route('companys.edit', $hq) }}"
                                            class="text-[10px] font-black uppercase text-blue-600 hover:underline">
                                            {{ __('Manage') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            {{-- Branch Sub-Rows [Addendum 3.c] --}}
                            @foreach ($hq->branches as $branch)
                                <tr>
                                    <td class="px-6 py-3 pl-16">
                                        <div class="flex items-center gap-2 text-gray-400 italic">
                                            <span>â†³</span>
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-xs font-bold text-gray-700">{{ $branch->company_name }}</span>
                                                <span
                                                    class="text-[9px] font-mono uppercase tracking-tighter">{{ $branch->branch_code }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        @if ($branch->catalog_id)
                                            <span
                                                class="px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-[9px] font-black uppercase">
                                                {{ $branch->catalog->name }}
                                            </span>
                                        @else
                                            <span class="text-[9px] text-gray-400 font-bold italic uppercase">
                                                {{ __('Inherited: ') }} {{ $hq->catalog->name ?? 'None' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-[10px] text-gray-400">
                                        {{ $branch->city }}, {{ $branch->state }}
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('companys.edit', $branch) }}"
                                            class="text-[10px] font-black uppercase text-gray-400 hover:text-blue-600">{{ __('Edit Branch') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <div class="p-6 border-t border-gray-100">
                    {{ $companys->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
