{{--
    Donut chart drawn as SVG ring segments, clockwise from 12 o'clock.
    $segments: list of ['label' => string, 'value' => int, 'color' => hex].
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

    foreach ($segments as $segment) {
        if ($segment['value'] <= 0 || $total === 0) {
            continue;
        }

        $sweep = 2 * M_PI * $segment['value'] / $total;
        $endAngle = $startAngle + min($sweep, 2 * M_PI - 0.0001);
        $largeArc = $sweep > M_PI ? 1 : 0;

        $paths[] = [
            'color' => $segment['color'],
            'd' => 'M '.$point($outerRadius, $startAngle)
                .' A '.$outerRadius.' '.$outerRadius.' 0 '.$largeArc.' 1 '.$point($outerRadius, $endAngle)
                .' L '.$point($innerRadius, $endAngle)
                .' A '.$innerRadius.' '.$innerRadius.' 0 '.$largeArc.' 0 '.$point($innerRadius, $startAngle)
                .' Z',
        ];

        $startAngle += $sweep;
    }
@endphp

<div {{ $attributes->class(['relative shrink-0']) }} style="width: {{ $size }}px; height: {{ $size }}px">
    <svg viewBox="0 0 163 163" class="size-full" role="img" aria-label="{{ $caption }}: {{ $total }}">
        @foreach ($paths as $path)
            <path d="{{ $path['d'] }}" fill="{{ $path['color'] }}" stroke="white" stroke-width="3" />
        @endforeach
    </svg>
    <div class="absolute inset-0 flex flex-col items-center justify-center">
        <span class="text-[28px] leading-8 font-bold text-ink">{{ $total }}</span>
        <span class="text-xs text-muted">{{ $caption }}</span>
    </div>
</div>
