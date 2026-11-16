{{--
    Image that opens the shared zoom popup (<x-image-zoom>, app.js [data-perbesar-gambar]) when pressed.
    The button fills its container, so place it where the bare <img> used to be. Classes and attributes go to the <img>.
--}}
@props(['src', 'alt'])

<button
    type="button"
    class="flex size-full cursor-zoom-in items-center justify-center focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-primary"
    aria-label="Perbesar {{ $alt }}"
    data-perbesar-gambar
>
    <img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes }}>
</button>
