@props([
    'href',
    'label',
    'count' => null,
    'color' => null,
])

@php
    $isRed = $count && $count > 0;
    $badgeColor = $color ?? ($isRed ? 'red' : 'green');
@endphp

<div>
    <a href="{{ $href }}" class="text-blue-600 hover:underline">
        {{ $label }}
    </a>
    @if(!is_null($count))
        <span class="inline-block bg-{{ $badgeColor }}-100 text-{{ $badgeColor }}-800 text-xs font-semibold px-2.5 py-0.5 rounded-full ml-1">
            {{ $count }}
        </span>
    @endif
</div>
