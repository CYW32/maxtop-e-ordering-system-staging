@props(['active'])

@php
    $baseClasses =
        'flex items-center w-full px-4 py-3 justify-start text-sm rounded-xl font-bold transition-all duration-200 ease-in-out cursor-pointer overflow-hidden ';

    $activeClasses =
        $active ?? false
            ? 'bg-white/20 text-[#FFFFFF] shadow-sm'
            : 'text-white/70 hover:text-[#FFFFFF] hover:bg-white/10';
@endphp

<a {{ $attributes->merge(['class' => $baseClasses . $activeClasses]) }} title="{{ trim(strip_tags((string) $slot)) }}">
    <span class="whitespace-nowrap">
        {{ $slot }}
    </span>
</a>
