{{-- One option in the Kategori dropdown of the alat form. Also cloned by app.js when a kategori is added from the popup. --}}
<label class="group flex h-8 shrink-0 cursor-pointer items-center justify-between rounded-md px-2.5 text-sm text-ink hover:bg-canvas has-checked:bg-canvas">
    <input type="radio" name="id_kategori_alat" value="{{ $idKategori }}" class="sr-only" @checked($terpilih) required>
    <span data-option-badge>{{ $namaKategori }}</span>
    <x-icon name="centang" class="invisible size-5 text-primary-dark group-has-checked:visible" />
</label>
