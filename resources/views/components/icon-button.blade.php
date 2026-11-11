{{-- Square 40px "+" button from the design system. Renders an <a> when href is given. --}}
@props(['href' => null])

@php($classes = 'flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary text-white transition-colors hover:bg-primary-dark')

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        <x-icon name="tambah" class="size-5" />
    </a>
@else
    <button type="button" {{ $attributes->class($classes) }}>
        <x-icon name="tambah" class="size-5" />
    </button>
@endif
