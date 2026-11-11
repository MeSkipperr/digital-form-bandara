@php
    // Sample data from the Figma design. Replace with real queries when the backend is wired up.
    $warnaStatus = [
        'Berhasil' => '#16A34A',
        'Gagal' => '#DC2626',
        'Menunggu' => '#64748B',
        'Dilewati' => '#2563EB',
    ];

    $daftarRiwayat = [
        1 => ['status' => 'Berhasil', 'judul' => 'Form Xray Atas', 'alat' => 'XRAY-01', 'model' => 'Xray Pro Max', 'catatan' => 'Semua item lolos', 'pesan' => 'Selesai pukul 08:42', 'petugas' => 'Yoga Pratama', 'waktu' => '01 Nov 2026, 08:42', 'gambar' => 'images/contoh/form-xray-atas.svg'],
        2 => ['status' => 'Gagal', 'judul' => 'Form Xray Samping', 'alat' => 'BOMB-01', 'model' => 'Itemiser 4DX', 'catatan' => 'Sensor samping tidak merespons', 'pesan' => 'Menunggu teknisi', 'petugas' => 'Yoga Pratama', 'waktu' => '01 Nov 2026, 09:14', 'gambar' => 'images/contoh/form-xray-samping.svg'],
        3 => ['status' => 'Menunggu', 'judul' => 'Form Kelistrikan', 'alat' => 'XRAY-02', 'model' => 'Xray Pro Max', 'catatan' => 'Belum diisi petugas', 'pesan' => 'Belum ada pesan', 'petugas' => '-', 'waktu' => '-', 'gambar' => 'images/contoh/form-kelistrikan.svg'],
        4 => ['status' => 'Dilewati', 'judul' => 'Form Kebersihan Lensa', 'alat' => 'XRAY-03', 'model' => 'Xray Scan 200', 'catatan' => 'Alat sedang dipakai', 'pesan' => 'Dilewati oleh Made', 'petugas' => 'Made Dwi Astuti', 'waktu' => '01 Nov 2026, 09:30', 'gambar' => 'images/contoh/form-kebersihan-lensa.svg'],
        5 => ['status' => 'Berhasil', 'judul' => 'Form Xray Atas', 'alat' => 'BOMB-02', 'model' => 'IONSCAN 600', 'catatan' => 'Kondisi normal', 'pesan' => 'Selesai pukul 09:10', 'petugas' => 'Komang Ayu Lestari', 'waktu' => '01 Nov 2026, 09:10', 'gambar' => 'images/contoh/form-xray-atas.svg'],
        6 => ['status' => 'Gagal', 'judul' => 'Form Kalibrasi', 'alat' => 'METL-01', 'model' => 'Garrett PD 6500i', 'catatan' => 'Hasil di luar batas', 'pesan' => 'Perlu kalibrasi ulang', 'petugas' => 'Kadek Surya Dharma', 'waktu' => '01 Nov 2026, 09:45', 'gambar' => 'images/contoh/form-kelistrikan.svg'],
        7 => ['status' => 'Berhasil', 'judul' => 'Form Xray Atas', 'alat' => 'XRAY-03', 'model' => 'Xray Scan 200', 'catatan' => 'Kondisi normal', 'pesan' => 'Selesai pukul 10:05', 'petugas' => 'Wayan Sari Dewi', 'waktu' => '01 Nov 2026, 10:05', 'gambar' => 'images/contoh/form-xray-atas.svg'],
    ];
@endphp

<x-layouts.admin title="Riwayat Pengecekan" active="riwayat">
    <div class="flex items-center gap-3">
        <x-search-input placeholder="Cari form atau catatan" class="mr-2 w-85" />
        <x-form.select name="periode" :options="['1-7-nov-2026' => '1 - 7 Nov 2026']" surface="bg-white" class="w-42.5" />
        <x-form.select name="status" :options="['' => 'Semua status'] + array_combine(array_keys($warnaStatus), array_keys($warnaStatus))" :selected="request()->query('status')" surface="bg-white" class="w-42.5" />
        <x-form.select name="alat" :options="['' => 'Semua alat', 'XRAY-01' => 'XRAY-01', 'XRAY-02' => 'XRAY-02', 'XRAY-03' => 'XRAY-03', 'BOMB-01' => 'BOMB-01', 'BOMB-02' => 'BOMB-02', 'METL-01' => 'METL-01']" :selected="request()->query('alat')" surface="bg-white" class="w-42.5" />
    </div>

    <x-card class="mt-5 overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-line-soft">
                <tr>
                    <x-table-heading class="w-36.5">Status</x-table-heading>
                    <x-table-heading class="w-65 px-0">Judul</x-table-heading>
                    <x-table-heading class="w-67.5 px-0">Catatan</x-table-heading>
                    <x-table-heading class="px-0">Pesan</x-table-heading>
                    <x-table-heading class="w-22 pl-0 text-right">Gambar</x-table-heading>
                </tr>
            </thead>
            <tbody class="divide-y divide-line-soft">
                @foreach ($daftarRiwayat as $id => $riwayat)
                    <tr class="h-18 cursor-pointer hover:bg-hover/60" data-modal-open="panel-riwayat-{{ $id }}" tabindex="0">
                        <td class="px-6"><x-badge :color="$warnaStatus[$riwayat['status']]" :label="$riwayat['status']" class="min-w-17.5" /></td>
                        <td>
                            <p class="text-sm text-ink">{{ $riwayat['judul'] }}</p>
                            <p class="mt-1 font-mono text-xs text-muted">{{ $riwayat['alat'] }}</p>
                        </td>
                        <td class="text-sm text-ink">{{ $riwayat['catatan'] }}</td>
                        <td class="text-sm text-ink">{{ $riwayat['pesan'] }}</td>
                        <td class="px-6">
                            <img src="{{ asset($riwayat['gambar']) }}" alt="Gambar {{ $riwayat['judul'] }}" class="ml-auto h-11 w-16 rounded-md border border-line object-cover">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <x-slot:modals>
        @foreach ($daftarRiwayat as $id => $riwayat)
            {{-- 07b: detail side panel. The overlay covers the content area only, the sidebar stays visible. --}}
            <dialog id="panel-riwayat-{{ $id }}" class="m-0 h-full max-h-none w-full max-w-none bg-transparent backdrop:bg-transparent" aria-labelledby="panel-riwayat-{{ $id }}-judul">
                <div class="fixed inset-y-0 right-108 left-60 bg-overlay/45" data-modal-close></div>
                <aside class="fixed inset-y-0 right-0 flex w-108 flex-col overflow-y-auto border-l border-line bg-white px-6 pt-8 pb-11">
                    <div class="flex items-start justify-between">
                        <h2 id="panel-riwayat-{{ $id }}-judul" class="text-xl font-bold text-ink">Detail Pengecekan</h2>
                        <button type="button" class="text-body hover:text-ink" data-modal-close aria-label="Tutup">
                            <x-icon name="tutup" class="size-5" />
                        </button>
                    </div>

                    <x-badge :color="$warnaStatus[$riwayat['status']]" :label="$riwayat['status']" class="mt-5 self-start" />

                    <dl class="mt-7 flex flex-col gap-3.5">
                        @foreach ([
                            'Judul' => $riwayat['judul'],
                            'Alat' => $riwayat['alat'].', '.$riwayat['model'],
                            'Petugas' => $riwayat['petugas'],
                            'Waktu' => $riwayat['waktu'],
                        ] as $label => $nilai)
                            <div>
                                <dt class="text-xs text-muted">{{ $label }}</dt>
                                <dd class="mt-0.5 text-sm text-ink">{{ $nilai }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <dl class="mt-6 flex flex-col gap-5">
                        <div>
                            <dt class="text-xs text-muted">Catatan</dt>
                            <dd class="mt-0.5 text-sm text-ink">{{ $riwayat['catatan'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted">Pesan</dt>
                            <dd class="mt-0.5 text-sm text-ink">{{ $riwayat['pesan'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-muted">Gambar</dt>
                            <dd class="mt-2"><img src="{{ asset($riwayat['gambar']) }}" alt="Gambar {{ $riwayat['judul'] }}" class="h-47.5 w-full rounded-lg border border-line object-cover"></dd>
                        </div>
                    </dl>

                    <x-button :href="route('admin.alat.show', $riwayat['alat'])" class="mt-9 h-11 w-full shrink-0">Buka Detail Alat</x-button>
                </aside>
            </dialog>
        @endforeach
    </x-slot:modals>
</x-layouts.admin>
