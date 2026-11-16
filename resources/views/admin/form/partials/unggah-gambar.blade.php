{{--
    "Unggah Gambar" field from the 05b / 05c designs. Empty: a dashed drop zone. With an image: a preview with
    "Ganti gambar" and "Hapus gambar". app.js ([data-unggah-gambar]) handles drag and drop, preview and removal.

    @var string $idInput
    @var string|null $urlGambar  current image when editing
--}}
<div data-unggah-gambar data-url-asli="{{ $urlGambar }}">
    <x-form.label>Unggah Gambar</x-form.label>
    <input type="file" id="{{ $idInput }}" name="gambar" accept="image/png,image/jpeg" class="sr-only" data-unggah-input>
    <input type="hidden" name="hapus_gambar" value="0" data-unggah-hapus>

    <label
        for="{{ $idInput }}"
        class="flex h-28 cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-line bg-canvas transition-colors hover:border-primary/40 data-seret:border-primary data-seret:bg-primary/5"
        data-unggah-kosong
        @if ($urlGambar) hidden @endif
    >
        <span class="flex size-8 items-center justify-center rounded-full bg-primary text-white">
            <x-icon name="tambah" class="size-4.5" />
        </span>
        <span class="mt-2.5 text-[13px] font-bold text-ink">Tarik gambar ke sini atau pilih file</span>
        <span class="mt-1 text-xs text-muted">PNG atau JPG, maksimal 5 MB</span>
    </label>

    <div data-unggah-pratinjau @unless ($urlGambar) hidden @endunless>
        <div class="flex h-36 items-center justify-center rounded-lg border border-line-soft bg-canvas p-1.5">
            <img src="{{ $urlGambar }}" alt="Pratinjau gambar form" class="h-full max-w-full rounded-md object-contain" data-unggah-gambar-pratinjau>
        </div>
        <div class="mt-3 flex items-center gap-3">
            <label for="{{ $idInput }}" class="inline-flex h-9 cursor-pointer items-center justify-center gap-2 rounded-lg border border-line bg-white px-3.5 text-[13px] font-bold text-ink transition-colors hover:border-primary/40 hover:bg-hover">
                <x-icon name="unggah" class="size-4" />
                Ganti gambar
            </label>
            <x-button variant="secondary" class="h-9 px-3.5 text-[13px]" data-unggah-hapus-tombol>Hapus gambar</x-button>
        </div>
    </div>

    <p class="mt-1.5 text-xs text-danger" data-unggah-galat hidden></p>
</div>
