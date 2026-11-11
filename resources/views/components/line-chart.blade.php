{{--
    Area line chart for "Jumlah Form yang Diisi Setiap Hari".
    $points: list of ['label' => string, 'value' => int, 'tooltip' => string].
    $highlight: index of the point that shows the tooltip.
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
@endphp

<svg {{ $attributes->class(['w-full overflow-visible']) }} viewBox="0 0 700 236" role="img" aria-label="Grafik jumlah form per hari">
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

    @if ($highlight !== null)
        <line x1="{{ $coordinates[$highlight]['x'] }}" x2="{{ $coordinates[$highlight]['x'] }}" y1="{{ $coordinates[$highlight]['y'] }}" y2="{{ $plotBottom }}" stroke="#19A7A0" />
    @endif

    <polyline points="{{ $line }}" fill="none" stroke="#19A7A0" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />

    @foreach ($coordinates as $index => $coordinate)
        <circle cx="{{ $coordinate['x'] }}" cy="{{ $coordinate['y'] }}" r="4.75" @class(['stroke-[2.5]', 'fill-teal stroke-white' => $index === $highlight, 'fill-white stroke-teal' => $index !== $highlight]) />
        <text x="{{ $coordinate['x'] }}" y="{{ $plotBottom + 22 }}" text-anchor="middle" class="fill-muted text-xs">{{ $points[$index]['label'] }}</text>
    @endforeach

    @if ($highlight !== null)
        @php($tip = $coordinates[$highlight])
        @php($tipX = min(max($tip['x'], $plotLeft + 54), $plotLeft + $plotWidth + 16 - 54))
        <g>
            <rect x="{{ $tipX - 54 }}" y="{{ $tip['y'] - 54 }}" width="108" height="40" rx="6" fill="#12345A" />
            <text x="{{ $tipX }}" y="{{ $tip['y'] - 37 }}" text-anchor="middle" class="fill-sidebar-text text-[11px]">{{ $points[$highlight]['tooltip'] }}</text>
            <text x="{{ $tipX }}" y="{{ $tip['y'] - 21 }}" text-anchor="middle" class="fill-white text-[13px] font-bold">{{ $points[$highlight]['value'] }} form</text>
        </g>
    @endif
</svg>
