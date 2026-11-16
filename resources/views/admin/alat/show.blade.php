@php
    $status = $daftarStatus[$alat->status];
    $namaKategori = $alat->kategoriAlat->nama;
@endphp

<x-layouts.admin title="Detail Alat" active="alat" breadcrumb="Inspeksi Alat / Alat / Detail">
    <x-back-button :href="route('admin.alat.index')" />

    <x-alert-messages class="mt-4" />

    <div class="mt-5 grid grid-cols-[minmax(0,2fr)_minmax(0,1fr)] gap-5">
        <x-card class="px-6 pt-6 pb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-2xl leading-8 font-bold text-primary-dark">{{ $alat->model ?? $alat->id }}</h2>
                    <p class="mt-1 font-mono text-sm text-muted">{{ $alat->id }}</p>
                    @if ($alat->lokasi)
                        <p class="mt-2 flex items-center gap-1.5 text-sm text-body">
                            <x-icon name="lokasi" class="size-4 text-muted" />
                            {{ $alat->lokasi }}
                        </p>
                    @endif
                </div>
                <x-badge :color="$status['warna']" :label="$status['label']" class="mt-1" />
            </div>

            <div class="mt-5 flex items-center gap-3 border-y border-line-soft py-4">
                <x-kategori-ikon :nama="$namaKategori" class="size-11" />
                <div>
                    <p class="text-xs text-muted">Kategori Alat</p>
                    <p class="text-base font-bold text-ink">{{ $namaKategori }}</p>
                </div>
            </div>

            <dl class="mt-5 grid grid-cols-2 gap-x-8 gap-y-5">
                <div>
                    <dt class="text-xs text-muted">ID Alat</dt>
                    <dd class="mt-1 font-mono text-sm font-bold text-ink">{{ $alat->id }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Model</dt>
                    <dd class="mt-1 text-sm font-bold text-ink">{{ $alat->model ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Status</dt>
                    <dd class="mt-1"><x-badge :color="$status['warna']" :label="$status['label']" /></dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Lokasi</dt>
                    <dd class="mt-1 text-sm font-bold text-ink">{{ $alat->lokasi ?? '-' }}</dd>
                </div>
            </dl>

            <x-button variant="secondary" class="mt-6 w-48 font-bold" data-modal-open="modal-ubah-alat">
                <x-icon name="pensil" class="size-4" />
                Ubah Informasi
            </x-button>
        </x-card>

        <x-card class="flex flex-col items-center px-6 pt-7 pb-6">
            <x-qr-code id="qr-alat" :value="$alat->id" :size="200" />
            <p class="mt-6 font-mono text-2xl font-bold text-ink">{{ $alat->id }}</p>
            <x-button class="mt-auto h-11 w-full" data-unduh-qr="qr-alat" data-nama-file="qr-{{ $alat->id }}">
                <x-icon name="unduh" class="size-4" />
                Unduh / Cetak QR
            </x-button>
        </x-card>
    </div>

    <x-card class="mt-5 px-6 pt-5 pb-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-ink">Form Pemeriksaan</h2>
                <p class="mt-1 text-xs text-muted">Form untuk kategori {{ $namaKategori }}</p>
            </div>
            <x-button :href="route('admin.alat.form.index', $alat)" class="w-45">
                <x-icon name="form" class="size-4" />
                Lihat Semua Form
            </x-button>
        </div>

        <div class="mt-5 grid grid-cols-4 gap-4">
            @forelse ($daftarFormAlat as $formAlat)
                @php($urlGambar = $formAlat->templateForm->urlGambar())
                <div class="flex flex-col rounded-lg border border-line p-2.5">
                    <div class="flex aspect-[328/120] w-full items-center justify-center overflow-hidden rounded-md bg-primary-dark text-sidebar-icon">
                        @if ($urlGambar)
                            <img src="{{ $urlGambar }}" alt="Gambar {{ $formAlat->templateForm->nama }}" class="size-full object-cover">
                        @else
                            <x-icon name="form" class="size-7" />
                        @endif
                    </div>
                    <p class="mt-4 px-1 text-sm font-bold text-ink">{{ $formAlat->templateForm->nama }}</p>
                    <x-button variant="secondary" :href="route('admin.alat.form.task', [$alat, $formAlat])" class="mt-3 h-9 w-full">Detail</x-button>
                </div>
            @empty
                <p class="col-span-4 rounded-lg border border-dashed border-line px-6 py-8 text-center text-sm text-muted">
                    Belum ada form yang dipasang. Buka "Lihat Semua Form" untuk menambahkan form.
                </p>
            @endforelse
        </div>
    </x-card>

    <x-slot:modals>
        <x-modal id="modal-ubah-alat" title="Ubah Alat">
            @include('admin.alat.partials.form-alat', ['alat' => $alat])
        </x-modal>

        @include('admin.alat.partials.modal-kelola-kategori')
    </x-slot:modals>
</x-layouts.admin>
