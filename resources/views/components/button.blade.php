{{--
    Buttons from the design system: primary (blue), secondary (white outline) and danger (red).
    Renders an <a> when href is given, otherwise a <button>.
--}}
@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
    $classes = match ($variant) {
        'primary' => 'bg-primary font-bold text-white hover:bg-primary-dark',
        'danger' => 'bg-danger font-bold text-white hover:bg-danger-dark',
        default => 'border border-line bg-white text-ink hover:border-primary/40 hover:bg-hover',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class(['inline-flex h-10 items-center justify-center gap-2 rounded-lg px-4 text-sm transition-colors', $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class(['inline-flex h-10 items-center justify-center gap-2 rounded-lg px-4 text-sm transition-colors', $classes]) }}>
        {{ $slot }}
    </button>
@endif
