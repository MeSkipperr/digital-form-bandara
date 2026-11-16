{{-- Inline kategori name field with save (✓) and cancel (×), used for both renaming and adding a kategori. --}}
<div class="flex h-9 items-center gap-0.5 rounded-md border border-primary bg-white pr-1.5">
    <input type="text" class="h-full min-w-0 flex-1 bg-transparent pl-2.5 text-sm text-ink outline-none placeholder:text-muted" maxlength="100" placeholder="Nama kategori" aria-label="Nama kategori" data-kategori-input>
    <button type="button" class="flex size-7 items-center justify-center rounded-md text-success hover:bg-success/10 disabled:opacity-50" data-kategori-simpan aria-label="Simpan kategori" title="Simpan">
        <x-icon name="centang" class="size-4" />
    </button>
    <button type="button" class="flex size-7 items-center justify-center rounded-md text-muted hover:bg-canvas hover:text-ink" data-kategori-batal aria-label="Batal" title="Batal">
        <x-icon name="tutup" class="size-4" />
    </button>
</div>
<p class="mt-1 px-1 text-xs text-danger" data-kategori-galat hidden></p>
