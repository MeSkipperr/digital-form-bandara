@props(['segments'])

<ul {{ $attributes->class(['flex flex-col gap-3.5']) }}>
    @foreach ($segments as $segment)
        <li class="flex items-center gap-2.5 text-sm">
            <span class="size-2.5 shrink-0 rounded-full" style="background-color: {{ $segment['color'] }}"></span>
            <span class="flex-1 text-body">{{ $segment['label'] }}</span>
            <span class="font-bold text-ink">{{ $segment['value'] }}</span>
        </li>
    @endforeach
</ul>
