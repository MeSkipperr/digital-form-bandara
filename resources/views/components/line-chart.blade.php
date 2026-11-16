{{--
    Area line chart for "Jumlah Form yang Diisi Setiap Hari".
    $points: list of ['label' => string, 'value' => int, 'tooltip' => string].
    $highlight: index of the point that shows its tooltip while nothing is hovered.
    Every point carries its own guide line and tooltip; app.js ([data-line-chart]) moves data-aktif
    to the point nearest the pointer, or the one picked with the arrow keys.
--}}
@props([
    'points',
    'highlight' => null,
    'max' => 30,
    'step' => 10,
])

@php
    $plotLeft = 40;
    $plotTop = 50;
    $plotWidth = 644;
    $plotHeight = 158;
    $plotBottom = $plotTop + $plotHeight;
    $gap = count($points) > 1 ? $plotWidth / (count($points) - 1) : 0;

    $coordinates = array_map(fn (array $point, int $index): array => [
        'x' => round($plotLeft + $index * $gap, 2),
        'y' => round($plotBottom - ($point['value'] / $max) * $plotHeight, 2),
    ], $points, array_keys($points));

    $line = implode(' ', array_map(fn (array $c): string => $c['x'].','.$c['y'], $coordinates));
    $area = 'M '.$plotLeft.','.$plotBottom.' L '.$line.' L '.($plotLeft + $plotWidth).','.$plotBottom.' Z';

    $isActive = fn (int $index): bool => $index === $highlight;
@endphp

<svg
    {{ $attributes->class(['w-full touch-pan-y overflow-visible rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/30']) }}
    viewBox="0 0 700 236"
    role="img"
    aria-label="Grafik jumlah form per hari. Gunakan tombol panah untuk melihat setiap hari."
    tabindex="0"
    data-line-chart
    data-sorot="{{ $highlight ?? '' }}"
>
    <defs>
        <linearGradient id="line-chart-area" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#19A7A0" stop-opacity="0.3" />
            <stop offset="1" stop-color="#19A7A0" stop-opacity="0" />
        </linearGradient>
    </defs>

    @for ($value = 0; $value <= $max; $value += $step)
        @php($y = $plotBottom - ($value / $max) * $plotHeight)
        <line x1="{{ $plotLeft }}" x2="{{ $plotLeft + $plotWidth }}" y1="{{ $y }}" y2="{{ $y }}" stroke="#E6ECF4" />
        <text x="{{ $plotLeft - 16 }}" y="{{ $y + 4 }}" text-anchor="end" class="fill-muted text-xs">{{ $value }}</text>
    @endfor

    <path d="{{ $area }}" fill="url(#line-chart-area)" />

    @foreach ($coordinates as $index => $coordinate)
        <line
            x1="{{ $coordinate['x'] }}" x2="{{ $coordinate['x'] }}" y1="{{ $plotTop - 8 }}" y2="{{ $plotBottom }}"
            stroke="#19A7A0" stroke-dasharray="3 3"
            class="opacity-0 transition-opacity duration-150 data-aktif:opacity-100"
            data-chart-index="{{ $index }}"
            @if ($isActive($index)) data-aktif @endif
        />
    @endforeach

    <polyline points="{{ $line }}" fill="none" stroke="#19A7A0" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />

    @foreach ($coordinates as $index => $coordinate)
        <circle
            cx="{{ $coordinate['x'] }}" cy="{{ $coordinate['y'] }}" r="4.75"
            class="fill-white stroke-teal stroke-[2.5] transition-[fill,stroke] duration-150 data-aktif:fill-teal data-aktif:stroke-white"
            data-chart-index="{{ $index }}"
            data-x="{{ $coordinate['x'] }}"
            @if ($isActive($index)) data-aktif @endif
        />
        <text
            x="{{ $coordinate['x'] }}" y="{{ $plotBottom + 22 }}" text-anchor="middle"
            class="fill-muted text-xs data-aktif:fill-ink data-aktif:font-bold"
            data-chart-index="{{ $index }}"
            @if ($isActive($index)) data-aktif @endif
        >{{ $points[$index]['label'] }}</text>
    @endforeach

    @foreach ($coordinates as $index => $tip)
        @php($tipX = min(max($tip['x'], $plotLeft + 54), $plotLeft + $plotWidth + 16 - 54))
        @php($tipY = max($tip['y'] - 54, 0))
        <g
            class="pointer-events-none opacity-0 transition-opacity duration-150 data-aktif:opacity-100"
            data-chart-index="{{ $index }}"
            data-chart-tooltip
            @if ($isActive($index)) data-aktif @endif
        >
            <rect x="{{ $tipX - 54 }}" y="{{ $tipY }}" width="108" height="40" rx="6" fill="#12345A" />
            <text x="{{ $tipX }}" y="{{ $tipY + 17 }}" text-anchor="middle" class="fill-sidebar-text text-[11px]">{{ $points[$index]['tooltip'] }}</text>
            <text x="{{ $tipX }}" y="{{ $tipY + 33 }}" text-anchor="middle" class="fill-white text-[13px] font-bold">{{ $points[$index]['value'] }} form</text>
        </g>
    @endforeach
</svg>
