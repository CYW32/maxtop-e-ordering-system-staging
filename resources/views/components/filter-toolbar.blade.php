@props(['placeholder' => __('Search...'), 'showDates' => false])

<div x-data="{
    submit() { this.$refs.filterForm.submit() }
}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-8">
    <form x-ref="filterForm" action="{{ url()->current() }}" method="GET"
        class="flex flex-col md:flex-row gap-4 items-center">

        {{-- Smart Search Input --}}
        <div class="relative w-full md:grow">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" @keyup.enter="submit()"
                class="block w-full pl-10 pr-3 py-2 border-gray-200 rounded-xl text-sm focus:ring-maxtop focus:border-maxtop transition-all placeholder-gray-400"
                placeholder="{{ $placeholder }}">
        </div>

        {{-- Dynamic "Smart" Slot for Dropdowns (Role, Status, Category) --}}
        @if (!$slot->isEmpty())
            <div class="flex gap-2 w-full md:w-auto" @change="submit()">
                {{ $slot }}
            </div>
        @endif

        {{-- Contextual Date Ranges (Hidden by default) --}}
        @if ($showDates)
            <div class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}" @change="submit()"
                    class="text-xs border-gray-200 rounded-lg focus:ring-maxtop focus:border-maxtop">
                <span class="text-gray-300">-</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" @change="submit()"
                    class="text-xs border-gray-200 rounded-lg focus:ring-maxtop focus:border-maxtop">
            </div>
        @endif

        {{-- Reset Button --}}
        @if (request()->hasAny(['search', 'role', 'status', 'category', 'start_date', 'end_date']))
            <a href="{{ url()->current() }}"
                class="text-[10px] font-black uppercase text-gray-400 hover:text-red-600 transition-colors">
                {{ __('Clear All') }}
            </a>
        @endif
    </form>
</div>
