{{--
    Legend rows for a donut chart. Pass `for` with the donut's id to link hover between them.
--}}
@props(['segments', 'for' => null])

<ul {{ $attributes->class(['-mx-2 flex flex-col gap-1.5']) }} @if ($for) data-donut-legend="{{ $for }}" @endif>
    @foreach ($segments as $index => $segment)
        <li
            class="flex items-center gap-2.5 rounded-md px-2 py-1 text-sm transition-[background-color,opacity] duration-150 data-aktif:bg-hover data-redup:opacity-50"
            data-donut-segment="{{ $index }}"
            data-label="{{ $segment['label'] }}"
            data-value="{{ $segment['value'] }}"
        >
            <span class="size-2.5 shrink-0 rounded-full" style="background-color: {{ $segment['color'] }}"></span>
            <span class="flex-1 text-body">{{ $segment['label'] }}</span>
            <span class="font-bold text-ink">{{ $segment['value'] }}</span>
        </li>
    @endforeach
</ul>
