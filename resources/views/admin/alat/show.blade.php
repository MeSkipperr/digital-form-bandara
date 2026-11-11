@php
    $contoh = require resource_path('views/admin/alat/partials/contoh-data.php');
    $idAlat = request()->route('alat');
    $alat = $contoh['alat'][$idAlat] ?? abort(404);
    $status = $contoh['status'][$alat['status']];
    $daftarKategori = $contoh['kategori'];
    $daftarForm = array_slice($contoh['form'], 0, 3, true);
@endphp

<x-layouts.admin title="Detail Alat" active="alat">
    <x-back-button :href="route('admin.alat.index')" />

    <div class="mt-5 grid grid-cols-[360fr_752fr] gap-6">
        <x-card class="flex flex-col items-center px-6 pt-8 pb-7">
            <x-qr-code :value="$idAlat" />
            <p class="mt-8 font-mono text-2xl font-bold text-ink">{{ $idAlat }}</p>
            <p class="mt-1.5 text-xs text-muted">Pindai untuk membuka form pengecekan</p>
            <x-button class="mt-7 h-11 w-full">Unduh / Cetak QR</x-button>
        </x-card>

        <x-card class="px-8 pt-5.5 pb-8">
            <div class="flex items-center justify-between border-b border-line-soft pb-6">
                <h2 class="text-xl font-bold text-ink">Informasi Alat</h2>
                <x-button variant="secondary" class="h-9 w-20" data-modal-open="modal-ubah-alat">Ubah</x-button>
            </div>
            <dl class="divide-y divide-line-soft">
                @foreach ([
                    'Model' => $alat['model'],
                    'ID Alat' => $idAlat,
                    'Kategori' => $daftarKategori[$alat['id_kategori_alat']],
                    'Lokasi' => $alat['lokasi'],
                    'Status' => null,
                    'Deskripsi' => $alat['deskripsi'],
                    'Dibuat pada' => $alat['dibuat_pada'],
                ] as $label => $nilai)
                    <div class="flex items-start py-4.5">
                        <dt class="w-42 shrink-0 text-[13px] text-muted">{{ $label }}</dt>
                        <dd @class(['text-sm leading-[22px] whitespace-pre-line text-ink', 'font-mono' => $label === 'ID Alat'])>
                            @if ($label === 'Status')
                                <x-badge :color="$status['warna']" :label="$status['label']" />
                            @else
                                {{ $nilai }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </x-card>
    </div>

    <x-card class="mt-6 px-6 pt-5 pb-6">
        <div class="flex items-start justify-between">
            <div class="pt-2">
                <h2 class="text-base font-bold text-ink">Form Pengecekan</h2>
                <p class="mt-1.5 text-xs text-muted">{{ count($daftarForm) }} form terpasang pada alat ini</p>
            </div>
            <x-icon-button :href="route('admin.alat.form.index', $idAlat)" aria-label="Kelola form" />
        </div>

        <div class="mt-4 grid grid-cols-3 gap-7">
            @foreach ($daftarForm as $idForm => $form)
                <div @class([
                    'rounded-lg border-2 bg-canvas p-4',
                    'border-success' => $form['kondisi'] === 'baik',
                    'border-danger' => $form['kondisi'] !== 'baik',
                ])>
                    <img src="{{ asset($form['gambar']) }}" alt="Gambar {{ $form['nama'] }}" class="h-25 w-full rounded-md object-cover">
                    <div class="mt-4.5 flex items-end justify-between">
                        <div>
                            <p class="text-sm font-bold text-ink">{{ $form['nama'] }}</p>
                            <p class="mt-1 text-xs text-muted">{{ $form['keterangan'] }}</p>
                        </div>
                        <x-button variant="secondary" :href="route('admin.alat.form.task', [$idAlat, $idForm])" class="mt-6 h-9 w-20">Detail</x-button>
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>

    <x-slot:modals>
        <x-modal id="modal-ubah-alat" title="Ubah Alat">
            @include('admin.alat.partials.form-alat', ['idAlat' => $idAlat, 'alat' => $alat, 'daftarKategori' => $daftarKategori])
        </x-modal>

        @include('admin.alat.partials.modal-tambah-kategori')
    </x-slot:modals>
</x-layouts.admin>
