<x-layouts.admin title="Riwayat Pengecekan" breadcrumb="Inspeksi Alat / Riwayat" active="riwayat">
    <form method="get" class="grid grid-cols-2 items-center gap-3 sm:grid-cols-4 xl:flex" data-auto-submit>
        <x-search-input placeholder="Cari judul, alat, atau username" class="col-span-2 xl:mr-2 xl:w-75" />
        <x-form.select name="kategori" :options="['' => 'Semua kategori'] + $daftarKategori" :selected="$kategoriAktif" surface="bg-white" class="w-45" aria-label="Filter kategori" />
        <x-form.select name="status" :options="['' => 'Semua status'] + array_map(fn (array $status): string => $status['label'], $daftarStatus)" :selected="$statusAktif" surface="bg-white" class="w-42.5" aria-label="Filter status" />
    </form>

    <x-card class="mt-5 overflow-hidden">
        <x-table-scroll>
            <table class="w-full min-w-6xl xl:min-w-0">
                <thead class="border-b border-line-soft">
                    <tr>
                        <x-table-heading class="w-43">Waktu Pengecekan</x-table-heading>
                        <x-table-heading class="w-36.5 px-0">Status</x-table-heading>
                        <x-table-heading class="w-65.5 px-0">Judul</x-table-heading>
                        <x-table-heading class="w-34 px-0">Alat</x-table-heading>
                        <x-table-heading class="px-0">Catatan</x-table-heading>
                        <x-table-heading class="w-32 px-0">Di Cek Oleh</x-table-heading>
                        <x-table-heading class="w-28 text-right">Aksi</x-table-heading>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line-soft">
                    @forelse ($daftarRiwayat as $riwayat)
                        @php
                            $status = $daftarStatus[$riwayat->status];
                        @endphp
                        <tr class="h-17 hover:bg-hover/60">
                            <td class="px-6 text-sm text-ink">{{ $riwayat->dicek_pada?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td><x-badge :color="$status['warna']" :label="$status['label']" /></td>
                            <td class="pr-4 text-sm font-bold text-ink">{{ $riwayat->formAlat->templateForm->nama }}</td>
                            <td class="pr-4">
                                <p class="font-mono text-sm font-bold text-ink">{{ $riwayat->formAlat->id_alat }}</p>
                                @if ($riwayat->formAlat->alat->model)
                                    <p class="mt-0.5 text-xs text-muted">{{ $riwayat->formAlat->alat->model }}</p>
                                @endif
                            </td>
                            <td class="pr-4 text-sm text-ink">{{ $riwayat->catatan ?? '-' }}</td>
                            <td class="text-sm text-ink">{{ $riwayat->id_pengguna ?? '-' }}</td>
                            <td class="px-6 text-right">
                                <button type="button" class="inline-flex h-8 items-center justify-center rounded-lg border border-line bg-white px-4 text-sm text-ink transition-colors hover:border-primary/40 hover:bg-hover" data-modal-open="modal-riwayat-{{ $riwayat->id }}">Detail</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-muted">Tidak ada riwayat pengecekan yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-scroll>
    </x-card>

    <x-pagination :paginator="$daftarRiwayat" label="catatan riwayat" class="mt-4" />

    <x-slot:modals>
        @foreach ($daftarRiwayat as $riwayat)
            @php
                $status = $daftarStatus[$riwayat->status];
                $judul = $riwayat->formAlat->templateForm->nama;
                $gambar = ltrim((string) $riwayat->formAlat->templateForm->path_gambar, '/');
                $urlGambar = $gambar !== '' && Storage::disk('public')->exists($gambar) ? Storage::disk('public')->url($gambar) : null;
                $model = $riwayat->formAlat->alat->model;
            @endphp
            <x-modal id="modal-riwayat-{{ $riwayat->id }}" title="Detail Riwayat Pengecekan" :description="$riwayat->kode()" width="w-190">
                <h3 class="border-b border-line-soft pb-2.5 text-sm font-bold text-primary-dark">Informasi Pengecekan</h3>
                <dl class="divide-y divide-line-soft border-b border-line-soft">
                    <div class="flex min-h-10 items-center gap-3 py-2 sm:h-10 sm:gap-0 sm:py-0">
                        <dt class="w-2/5 shrink-0 text-xs text-muted sm:w-51">Waktu Pengecekan</dt>
                        <dd class="text-sm font-bold text-ink">{{ $riwayat->dicek_pada?->format('d/m/Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="flex min-h-10 items-center gap-3 py-2 sm:h-10 sm:gap-0 sm:py-0">
                        <dt class="w-2/5 shrink-0 text-xs text-muted sm:w-51">Status</dt>
                        <dd><x-badge :color="$status['warna']" :label="$status['label']" /></dd>
                    </div>
                    <div class="flex min-h-10 items-center gap-3 py-2 sm:h-10 sm:gap-0 sm:py-0">
                        <dt class="w-2/5 shrink-0 text-xs text-muted sm:w-51">Judul</dt>
                        <dd class="text-sm font-bold text-ink">{{ $judul }}</dd>
                    </div>
                    <div class="flex min-h-10 items-center gap-3 py-2 sm:h-10 sm:gap-0 sm:py-0">
                        <dt class="w-2/5 shrink-0 text-xs text-muted sm:w-51">Alat</dt>
                        <dd class="text-sm font-bold text-ink">{{ $riwayat->formAlat->id_alat }}{{ $model ? ' — '.$model : '' }}</dd>
                    </div>
                    <div class="flex min-h-10 items-center gap-3 py-2.5 sm:gap-0">
                        <dt class="w-2/5 shrink-0 text-xs text-muted sm:w-51">Catatan</dt>
                        <dd class="text-sm font-bold text-ink">{{ $riwayat->catatan ?? '-' }}</dd>
                    </div>
                    <div class="flex min-h-10 items-center gap-3 py-2 sm:h-10 sm:gap-0 sm:py-0">
                        <dt class="w-2/5 shrink-0 text-xs text-muted sm:w-51">Di Cek Oleh</dt>
                        <dd class="text-sm font-bold text-ink">{{ $riwayat->id_pengguna ?? '-' }}</dd>
                    </div>
                </dl>

                <h3 class="mt-8 text-sm font-bold text-primary-dark">Dokumentasi Gambar</h3>
                <div class="mt-3 aspect-video rounded-lg bg-primary-dark p-3 sm:aspect-auto sm:h-54">
                    <div class="flex size-full items-center justify-center overflow-hidden rounded-md border border-dashed border-sidebar-muted">
                        @if ($urlGambar)
                            <x-zoomable-image :src="$urlGambar" alt="Dokumentasi {{ $judul }}" class="size-full object-contain" />
                        @else
                            <p class="text-sm text-sidebar-muted">Belum ada dokumentasi gambar.</p>
                        @endif
                    </div>
                </div>

                <x-slot:footer class="mt-5">
                    <x-button variant="secondary" class="flex-1 sm:w-25 sm:flex-none" data-modal-close>Tutup</x-button>
                    <x-button :href="route('admin.alat.show', $riwayat->formAlat->id_alat)" class="flex-1 sm:w-46 sm:flex-none">Lihat Detail Alat</x-button>
                </x-slot:footer>
            </x-modal>
        @endforeach
    </x-slot:modals>
</x-layouts.admin>
