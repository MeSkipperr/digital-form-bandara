{{--
    Square icon-only button from the design system. Renders an <a> when href is given.
    primary: 40px blue "+" button. secondary / danger: 36px outlined buttons, e.g. ubah (pensil) and hapus on cards.
--}}
@props(['href' => null, 'icon' => 'tambah', 'variant' => 'primary'])

@php
    $classes = match ($variant) {
        'primary' => 'size-10 bg-primary text-white hover:bg-primary-dark',
        'danger' => 'size-9 border border-line bg-white text-danger hover:border-danger/40 hover:bg-danger/5',
        default => 'size-9 border border-line bg-white text-ink hover:border-primary/40 hover:bg-hover',
    };
    $classes = 'flex shrink-0 items-center justify-center rounded-lg transition-colors '.$classes;
    $iconSize = $variant === 'primary' ? 'size-5' : 'size-4';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        <x-icon :name="$icon" :class="$iconSize" />
    </a>
@else
    <button type="button" {{ $attributes->class($classes) }}>
        <x-icon :name="$icon" :class="$iconSize" />
    </button>
@endif
