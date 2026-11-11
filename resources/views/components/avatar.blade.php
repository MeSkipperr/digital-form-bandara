{{-- Round initials avatar; pass the size and font size through class, e.g. class="size-10 text-sm". --}}
@props(['initials', 'color' => '#1D4E89'])

<span {{ $attributes->merge(['class' => 'flex shrink-0 items-center justify-center rounded-full font-bold text-white']) }} style="background-color: {{ $color }}">
    {{ $initials }}
</span>
