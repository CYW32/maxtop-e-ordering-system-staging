@props(['actionUrl' => null, 'placeholder' => 'Search...'])

<form method="GET" action="{{ $actionUrl ?? url()->current() }}"
    class="flex flex-col md:flex-row gap-3 items-end md:items-center bg-gray-50 p-3 rounded-lg border border-gray-200">

    {{-- SEARCH INPUT --}}
    <div class="relative w-full md:w-64">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 20 20" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
        </div>
        <input type="text" name="search" value="{{ request('search') }}"
            class="block w-full p-2 pl-10 text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            placeholder="{{ $placeholder }}">
    </div>

    {{-- DYNAMIC SLOT (For extra dropdowns like Role, Status, Category) --}}
    {{-- This is the magic part. If we pass content here, it renders. --}}
    @if (!$slot->isEmpty())
        <div class="flex items-center gap-2">
            {{ $slot }}
        </div>
    @endif

    {{-- DATE RANGE INPUTS --}}
    <div class="flex items-center gap-2">
        <div class="relative">
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" title="Start Date">
        </div>
        <span class="text-gray-500">-</span>
        <div class="relative">
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" title="End Date">
        </div>
    </div>

    {{-- FILTER & RESET BUTTONS --}}
    <div class="flex gap-2">
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700 transition">
            Filter
        </button>

        {{-- Reset Link: clear all params --}}
        @if (request()->hasAny(['search', 'start_date', 'end_date']))
            <a href="{{ url()->current() }}"
                class="bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-md text-sm hover:bg-gray-50">
                Clear
            </a>
        @endif
    </div>
</form>
