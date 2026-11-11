{{-- Status badge: a tinted pill with a colored dot. The color is any hex value, so custom statuses work too. --}}
@props(['color', 'label' => null])

<span {{ $attributes->merge(['class' => 'inline-flex h-6 items-center gap-2 rounded-md bg-(--badge)/12 px-2.5 text-xs whitespace-nowrap text-(--badge)']) }} style="--badge: {{ $color }}">
    <span class="size-1.5 shrink-0 rounded-full bg-(--badge)"></span>
    <span>{{ $label ?? $slot }}</span>
</span>
