@props(['href' => null, 'danger' => false])

@php($classes = ['flex h-8 w-full items-center rounded-md px-3.5 text-left text-sm hover:bg-line', 'text-danger' => $danger, 'text-ink' => ! $danger])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="button" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
