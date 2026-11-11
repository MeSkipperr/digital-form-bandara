{{-- Category icon tile. Shows the image at $url; falls back to the scan icon when there is no URL or it fails to load. --}}
@props(['url' => null, 'nama' => ''])

<span {{ $attributes->class(['relative flex shrink-0 items-center justify-center overflow-hidden rounded-lg border border-line bg-canvas text-primary']) }}>
    <x-icon name="alat" class="size-1/2" />
    @if ($url)
        <img src="{{ $url }}" alt="Ikon {{ $nama }}" class="absolute inset-0 size-full bg-canvas object-contain p-1.5" onerror="this.hidden = true">
    @endif
</span>
