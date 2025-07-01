{{-- resources/views/components/icon/download-pdf.blade.php --}}
{{-- Credit: Lucide (https://lucide.dev) --}}
@props([
    'variant' => 'outline', // support for size variants
])

@php
    if ($variant === 'solid') {
        throw new \Exception('The "solid" variant is not supported in Lucide.');
    }

    $classes = \Flux::classes('shrink-0')->add(
        match ($variant) {
            'outline' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
        },
    );

    $strokeWidth = match ($variant) {
        'outline' => 2,
        'mini' => 2.25,
        'micro' => 2.5,
    };
@endphp

{{-- Credit: Lucide (https://lucide.dev) --}}
@props([
    'variant' => 'outline',
])

@php
    if ($variant === 'solid') {
        throw new \Exception('The "solid" variant is not supported in Lucide.');
    }

    $classes = Flux::classes('shrink-0')->add(
        match ($variant) {
            'outline' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
        },
    );

    $strokeWidth = match ($variant) {
        'outline' => 2,
        'mini' => 2.25,
        'micro' => 2.5,
    };
@endphp
<svg 
    xmlns="http://www.w3.org/2000/svg"
    width="24"
    height="24"
    viewBox="0 0 24 24"
    class="{{ $attributes->merge(['class' => 'w-6 h-6'])['class'] }}"
    fill="none"
>
    {{-- Place your essential path data here. Example: --}}
    <path fill="#333" d="M3 3h18v18H3z" />
    <text x="12" y="16" text-anchor="middle" fill="#fff" font-size="6" font-family="Arial">PDF</text>
    {{-- Add more simplified paths if needed --}}
</svg>