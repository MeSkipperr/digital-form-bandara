{{--
    One kategori row with three modes: lihat (name + ubah / hapus buttons), ubah (inline rename) and hapus (confirmation
    that moves the alat and forms still using it to another kategori). Also cloned by app.js when a kategori is added.

    @var int|string $idKategori
    @var string $namaKategori
    @var int $jumlahAlat
    @var int $jumlahForm
    @var bool $terpilih
    @var bool $dapatDipilih
--}}
<div data-kategori-opsi data-kategori-id="{{ $idKategori }}" data-jumlah-alat="{{ $jumlahAlat }}" data-jumlah-form="{{ $jumlahForm }}">
    <div class="group flex h-9 items-center gap-0.5 rounded-md pr-1.5 hover:bg-canvas has-checked:bg-line-soft has-focus-visible:ring-2 has-focus-visible:ring-primary/40" data-mode="lihat">
        @if ($dapatDipilih)
            <label class="flex h-full min-w-0 flex-1 cursor-pointer items-center justify-between gap-2 pl-2.5 text-sm text-ink has-checked:font-bold has-checked:text-primary-dark">
                <input type="radio" name="id_kategori_alat" value="{{ $idKategori }}" class="sr-only" @checked($terpilih) required>
                <span class="truncate" data-option-badge data-kategori-nama>{{ $namaKategori }}</span>
                <x-icon name="centang" class="invisible size-4 text-primary group-has-checked:visible" />
            </label>
        @else
            <span class="min-w-0 flex-1 truncate pl-2.5 text-sm text-ink" data-kategori-nama>{{ $namaKategori }}</span>
        @endif
        <button type="button" class="ml-1 flex size-7 items-center justify-center rounded-md text-muted hover:bg-white hover:text-primary" data-kategori-ubah aria-label="Ubah kategori" title="Ubah">
            <x-icon name="pensil" class="size-3.5" />
        </button>
        <button type="button" class="flex size-7 items-center justify-center rounded-md text-muted hover:bg-white hover:text-danger" data-kategori-hapus aria-label="Hapus kategori" title="Hapus">
            <x-icon name="hapus" class="size-3.5" />
        </button>
    </div>

    <div data-mode="ubah" hidden>
        @include('admin.alat.partials.input-kategori')
    </div>

    <div class="rounded-md border border-warning/25 bg-amber-50 p-3" data-mode="hapus" hidden>
        <p class="text-xs leading-5 text-amber-900" data-kategori-pesan></p>
        <label class="mt-2 block" data-kategori-pindah>
            <span class="mb-1 block text-xs font-bold text-amber-900">Pindahkan ke</span>
            <span class="relative block">
                <select class="h-9 w-full appearance-none rounded-md border border-line bg-white pr-9 pl-3 text-sm text-ink outline-none focus:border-primary" data-kategori-tujuan></select>
                <x-icon name="chevron-bawah" class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-muted" />
            </span>
        </label>
        <p class="mt-2 text-xs text-danger" data-kategori-galat hidden></p>
        <div class="mt-2.5 flex items-center justify-end gap-2">
            <button type="button" class="h-7 rounded-md border border-line bg-white px-3.5 text-xs text-ink hover:bg-hover" data-kategori-batal>Batal</button>
            <button type="button" class="h-7 rounded-md bg-danger px-3.5 text-xs font-bold text-white hover:bg-danger-dark disabled:cursor-not-allowed disabled:opacity-50" data-kategori-konfirmasi-hapus>Hapus</button>
        </div>
    </div>
</div>
