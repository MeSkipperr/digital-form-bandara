{{--
    Full-screen image popup shared by every <x-zoomable-image> on the page. Rendered once by the admin layout.
    app.js ([data-perbesar]) zooms with the mouse wheel, a two-finger pinch, double click / tap and the toolbar buttons,
    and drags the zoomed image around. "Tutup", Escape or pressing the dark area around the image closes it.
--}}
@php
    $tombolAlat = 'flex size-9 items-center justify-center rounded-md text-white transition-colors hover:bg-white/15 disabled:cursor-not-allowed disabled:opacity-40 sm:size-10';
@endphp

<dialog
    id="modal-perbesar-gambar"
    class="m-0 size-full max-h-none max-w-none overflow-hidden bg-overlay/95 p-0 text-white backdrop-blur-md backdrop:bg-transparent"
    aria-label="Perbesar gambar"
    data-perbesar
>
    <div class="flex size-full flex-col">
        <div class="flex items-center gap-2 px-3 py-3 sm:gap-3 sm:px-6 sm:py-4">
            <p class="min-w-0 flex-1 truncate text-sm font-bold" data-perbesar-judul></p>

            <div class="flex items-center gap-0.5 rounded-lg bg-white/10 p-1">
                <button type="button" class="{{ $tombolAlat }}" aria-label="Perkecil" title="Perkecil" data-perbesar-aksi="perkecil">
                    <x-icon name="perkecil" class="size-5" />
                </button>
                <output class="min-w-14 text-center font-mono text-xs tabular-nums" aria-live="polite" data-perbesar-skala>100%</output>
                <button type="button" class="{{ $tombolAlat }}" aria-label="Perbesar" title="Perbesar" data-perbesar-aksi="perbesar">
                    <x-icon name="perbesar" class="size-5" />
                </button>
                <button type="button" class="{{ $tombolAlat }}" aria-label="Kembalikan ukuran" title="Kembalikan ukuran" data-perbesar-aksi="atur-ulang">
                    <x-icon name="atur-ulang" class="size-4.5" />
                </button>
            </div>

            <button
                type="button"
                class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white transition-colors hover:bg-white/20 sm:size-11"
                aria-label="Tutup"
                title="Tutup"
                autofocus
                data-modal-close
            >
                <x-icon name="tutup" class="size-5" />
            </button>
        </div>

        <div class="flex min-h-0 flex-1 touch-none items-center justify-center overflow-hidden px-3 pb-3 select-none sm:px-6 sm:pb-6" data-perbesar-panggung>
            <img alt="" class="max-h-full max-w-full cursor-zoom-in rounded-md object-contain data-halus:transition-transform data-halus:duration-200 data-halus:ease-out" draggable="false" data-perbesar-gambar-besar>
        </div>

        <p class="px-3 pb-3 text-center text-xs text-white/60 sm:pb-4">Scroll atau cubit untuk zoom, ketuk dua kali untuk memperbesar, seret untuk menggeser.</p>
    </div>
</dialog>
