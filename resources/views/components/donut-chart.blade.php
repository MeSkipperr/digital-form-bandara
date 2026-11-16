{{--
    Donut chart drawn as SVG ring segments, clockwise from 12 o'clock.
    $segments: list of ['label' => string, 'value' => int, 'color' => hex].
    Give it an id and pass the same id to <x-chart-legend for="..."> so hovering a legend row
    highlights its segment. app.js ([data-donut-chart]) swaps the centre text for the hovered segment.
--}}
@props([
    'segments',
    'caption',
    'size' => 160,
])

@php
    $total = array_sum(array_column($segments, 'value'));
    $outerRadius = 80;
    $innerRadius = 59;
    $center = 81.5;

    $point = fn (float $radius, float $angle): string => round($center + $radius * sin($angle), 3).' '.round($center - $radius * cos($angle), 3);

    $paths = [];
    $startAngle = 0.0;

    foreach ($segments as $index => $segment) {
        if ($segment['value'] <= 0 || $total === 0) {
            continue;
        }

        $sweep = 2 * M_PI * $segment['value'] / $total;
        $endAngle = $startAngle + min($sweep, 2 * M_PI - 0.0001);
        $largeArc = $sweep > M_PI ? 1 : 0;

        $paths[] = [
            'index' => $index,
            'color' => $segment['color'],
            'd' => 'M '.$point($outerRadius, $startAngle)
                .' A '.$outerRadius.' '.$outerRadius.' 0 '.$largeArc.' 1 '.$point($outerRadius, $endAngle)
                .' L '.$point($innerRadius, $endAngle)
                .' A '.$innerRadius.' '.$innerRadius.' 0 '.$largeArc.' 0 '.$point($innerRadius, $startAngle)
                .' Z',
        ];

        $startAngle += $sweep;
    }

    $percentage = fn (int $value): string => $total === 0 ? '0%' : round($value / $total * 100).'%';
@endphp

<div
    {{ $attributes->class(['relative shrink-0']) }}
    style="width: {{ $size }}px; height: {{ $size }}px"
    data-donut-chart
    data-total="{{ $total }}"
    data-caption="{{ $caption }}"
>
    <svg viewBox="0 0 163 163" class="size-full overflow-visible" role="img" aria-label="{{ $caption }}: {{ $total }}">
        @foreach ($paths as $path)
            @php($segment = $segments[$path['index']])
            <path
                d="{{ $path['d'] }}"
                fill="{{ $path['color'] }}"
                stroke="white"
                stroke-width="3"
                class="origin-center cursor-pointer transition-[opacity,scale] duration-150 transform-view data-aktif:scale-105 data-redup:opacity-35"
                data-donut-segment="{{ $path['index'] }}"
                data-label="{{ $segment['label'] }}"
                data-value="{{ $segment['value'] }}"
            ><title>{{ $segment['label'] }}: {{ $segment['value'] }} ({{ $percentage($segment['value']) }})</title></path>
        @endforeach
    </svg>
    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
        <span class="text-[28px] leading-8 font-bold text-ink" data-donut-value>{{ $total }}</span>
        <span class="text-xs text-muted" data-donut-caption>{{ $caption }}</span>
    </div>
</div>
