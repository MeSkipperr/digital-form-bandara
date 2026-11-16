{{--
    Scannable QR code whose only content is $value (for alat: the alat ID), drawn black on white.
    The SVG keeps the standard 4-module white quiet zone so printed labels and downloaded JPGs scan reliably.
    Each row's dark modules are merged into horizontal runs so the whole code is a single <path>.
--}}
@props(['value', 'size' => 232])

@php
    $matrix = \BaconQrCode\Encoder\Encoder::encode((string) $value, \BaconQrCode\Common\ErrorCorrectionLevel::M())->getMatrix();
    $quietZone = 4;
    $modules = $matrix->getWidth() + $quietZone * 2;
    $darkModulesPath = '';

    for ($y = 0; $y < $matrix->getHeight(); $y++) {
        for ($x = 0; $x < $matrix->getWidth(); $x++) {
            if ($matrix->get($x, $y) !== 1) {
                continue;
            }

            $run = 1;

            while ($x + $run < $matrix->getWidth() && $matrix->get($x + $run, $y) === 1) {
                $run++;
            }

            $darkModulesPath .= 'M'.($x + $quietZone).' '.($y + $quietZone).'h'.$run.'v1h-'.$run.'z';
            $x += $run - 1;
        }
    }
@endphp

<svg {{ $attributes }} xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $modules }} {{ $modules }}" shape-rendering="crispEdges" role="img" aria-label="Kode QR {{ $value }}">
    <rect width="{{ $modules }}" height="{{ $modules }}" fill="#FFFFFF" />
    <path d="{{ $darkModulesPath }}" fill="#000000" />
</svg>
