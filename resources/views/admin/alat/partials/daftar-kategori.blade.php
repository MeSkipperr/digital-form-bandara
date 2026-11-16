{{--
    Kategori list with inline tambah / ubah / hapus, from the "02-tambah-alat" design.
    With $dapatDipilih it is the Kategori dropdown of the alat form (radio name `id_kategori_alat`);
    without it, it is the plain list inside the Kelola Kategori modal.
    Changes are saved by app.js with fetch and mirrored into every list on the page.

    @var \Illuminate\Support\Collection<int, \App\Models\KategoriAlat> $daftarKategori  loaded with alat_count and template_form_count
    @var bool $dapatDipilih
    @var int|null $kategoriTerpilih
--}}
@php
    $kategoriTerpilih ??= null;
    $namaTerpilih = $daftarKategori->firstWhere('id', $kategoriTerpilih)?->nama;
@endphp

@if ($dapatDipilih)
    <details data-dropdown data-select-badge class="group/pilih">
        <summary class="flex h-10 cursor-pointer items-center justify-between rounded-lg border border-line bg-canvas px-3.5 text-sm text-ink group-open/pilih:border-primary group-open/pilih:bg-white">
            <span class="truncate" data-select-display data-placeholder="Pilih kategori">
                @if ($namaTerpilih !== null)
                    <span>{{ $namaTerpilih }}</span>
                @else
                    <span class="text-muted">Pilih kategori</span>
                @endif
            </span>
            <x-icon name="chevron-bawah" class="size-4.5 text-muted transition-transform group-open/pilih:rotate-180" />
        </summary>
        <div class="mt-1.5 rounded-lg border border-line bg-white shadow-sm">
@endif

<div data-kategori-daftar data-url="{{ route('admin.kategori-alat.store') }}">
    <div @class(['flex flex-col gap-1 overflow-y-auto p-1.5', 'max-h-56' => $dapatDipilih, 'max-h-80' => ! $dapatDipilih]) data-kategori-options>
        @foreach ($daftarKategori as $kategori)
            @include('admin.alat.partials.opsi-kategori', [
                'idKategori' => $kategori->id,
                'namaKategori' => $kategori->nama,
                'jumlahAlat' => $kategori->alat_count ?? 0,
                'jumlahForm' => $kategori->template_form_count ?? 0,
                'terpilih' => $kategoriTerpilih !== null && (int) $kategori->id === (int) $kategoriTerpilih,
            ])
        @endforeach
    </div>

    <div class="px-1.5 pb-1.5" data-kategori-baru hidden>
        @include('admin.alat.partials.input-kategori')
    </div>

    <div class="flex flex-col gap-0.5 border-t border-line-soft p-1.5">
        <button type="button" class="flex h-9 items-center gap-2 rounded-md px-2.5 text-sm font-bold text-primary-dark hover:bg-canvas" data-kategori-tambah>
            <x-icon name="tambah" class="size-4 text-primary" />
            Tambah Kategori
        </button>
        @if ($dapatDipilih)
            <button type="button" class="flex h-9 items-center gap-2 rounded-md px-2.5 text-sm font-bold text-primary-dark hover:bg-canvas" data-modal-open="modal-kelola-kategori">
                <x-icon name="label" class="size-4 text-primary" />
                Buka Kelola Kategori
            </button>
        @endif
    </div>

    <template data-kategori-template>
        @include('admin.alat.partials.opsi-kategori', ['idKategori' => '', 'namaKategori' => '', 'jumlahAlat' => 0, 'jumlahForm' => 0, 'terpilih' => false])
    </template>
</div>

@if ($dapatDipilih)
        </div>
    </details>
@endif
