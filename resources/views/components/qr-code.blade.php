{{--
    Visual QR placeholder: three finder squares plus a pattern derived from $value.
    It is NOT a scannable code. Swap it for a real QR generator (needs a package such as
    bacon/bacon-qr-code, which has to be approved first) before printing labels.
--}}
@props(['value', 'size' => 232])

@php
    $modules = 25;
    $finderOrigins = [[0, 0], [$modules - 7, 0], [0, $modules - 7]];

    $isFinderArea = function (int $x, int $y) use ($finderOrigins): bool {
        foreach ($finderOrigins as [$originX, $originY]) {
            if ($x >= $originX - 1 && $x <= $originX + 7 && $y >= $originY - 1 && $y <= $originY + 7) {
                return true;
            }
        }

        return false;
    };

    $cells = [];

    for ($y = 0; $y < $modules; $y++) {
        for ($x = 0; $x < $modules; $x++) {
            if (! $isFinderArea($x, $y) && crc32($value.':'.$x.':'.$y) % 2 === 0) {
                $cells[] = [$x, $y];
            }
        }
    }
@endphp

<svg {{ $attributes }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $modules }} {{ $modules }}" shape-rendering="crispEdges" role="img" aria-label="Kode QR {{ $value }}">
    <g fill="#12345A">
        @foreach ($finderOrigins as [$originX, $originY])
            <path d="M{{ $originX }} {{ $originY }}h7v7h-7z M{{ $originX + 1 }} {{ $originY + 1 }}v5h5v-5z" fill-rule="evenodd" />
            <rect x="{{ $originX + 2 }}" y="{{ $originY + 2 }}" width="3" height="3" />
        @endforeach
        @foreach ($cells as [$x, $y])
            <rect x="{{ $x }}" y="{{ $y }}" width="1" height="1" />
        @endforeach
    </g>
</svg>
