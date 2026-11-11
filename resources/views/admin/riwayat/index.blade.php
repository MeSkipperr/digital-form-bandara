<x-layouts.admin title="Riwayat Pengecekan" active="riwayat">
    <form method="get" class="flex items-center gap-3" data-auto-submit>
        <x-search-input placeholder="Cari form atau catatan" class="mr-2 w-85" />
        <x-form.select name="periode" :options="$daftarPeriode" :selected="request()->query('periode')" surface="bg-white" class="w-42.5" aria-label="Filter periode" />
        <x-form.select name="status" :options="['' => 'Semua status'] + array_map(fn (array $status): string => $status['label'], $daftarStatus)" :selected="request()->query('status')" surface="bg-white" class="w-42.5" aria-label="Filter status" />
        <x-form.select name="alat" :options="['' => 'Semua alat'] + $daftarAlat" :selected="request()->query('alat')" surface="bg-white" class="w-42.5" aria-label="Filter alat" />
    </form>

    <x-card class="mt-5 overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-line-soft">
                <tr>
                    <x-table-heading class="w-36.5">Status</x-table-heading>
                    <x-table-heading class="w-65 px-0">Judul</x-table-heading>
                    <x-table-heading class="px-0">Catatan</x-table-heading>
                    <x-table-heading class="w-22 pl-0 text-right">Gambar</x-table-heading>
                </tr>
            </thead>
            <tbody class="divide-y divide-line-soft">
                @forelse ($daftarRiwayat as $riwayat)
                    @php
                        $id = $riwayat->id;
                        $status = $daftarStatus[$riwayat->status];
                        $judul = $riwayat->formAlat->templateForm->nama;
                        $gambar = $riwayat->formAlat->templateForm->path_gambar;
                    @endphp
                    <tr class="h-18 cursor-pointer hover:bg-hover/60" data-modal-open="panel-riwayat-{{ $id }}" tabindex="0">
                        <td class="px-6"><x-badge :color="$status['warna']" :label="$status['label']" class="min-w-17.5" /></td>
                        <td>
                            <p class="text-sm text-ink">{{ $judul }}</p>
                            <p class="mt-1 font-mono text-xs text-muted">{{ $riwayat->formAlat->id_alat }}</p>
                        </td>
                        <td class="text-sm text-ink">{{ $riwayat->catatan ?? '-' }}</td>
                        <td class="px-6">
                            @if ($gambar)
                                <img src="{{ asset('storage/'.$gambar) }}" alt="Gambar {{ $judul }}" class="ml-auto h-11 w-16 rounded-md border border-line object-cover">
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-muted">Tidak ada riwayat pengecekan yang cocok dengan filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    @if ($daftarRiwayat->hasPages())
        <div class="mt-6 flex items-center justify-between pr-9">
            <p class="text-xs text-muted">Menampilkan {{ $daftarRiwayat->count() }} dari {{ $daftarRiwayat->total() }} riwayat</p>
            <nav class="flex items-center gap-2 text-sm" aria-label="Halaman">
                @foreach (range(1, $daftarRiwayat->lastPage()) as $halaman)
                    <a
                        href="{{ $daftarRiwayat->url($halaman) }}"
                        @class([
                            'flex h-7 w-9 items-center justify-center rounded-md border',
                            'border-line bg-line text-primary' => $halaman === $daftarRiwayat->currentPage(),
                            'border-line bg-white text-muted hover:bg-hover' => $halaman !== $daftarRiwayat->currentPage(),
                        ])
                    >{{ $halaman }}</a>
                @endforeach
            </nav>
        </div>
    @endif

    <x-slot:modals>
        @foreach ($daftarRiwayat as $riwayat)
            @php
                $id = $riwayat->id;
                $status = $daftarStatus[$riwayat->status];
                $judul = $riwayat->formAlat->templateForm->nama;
                $gambar = $riwayat->formAlat->templateForm->path_gambar;
            @endphp
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

                    <x-badge :color="$status['warna']" :label="$status['label']" class="mt-5 self-start" />

                    <dl class="mt-7 flex flex-col gap-3.5">
                        @foreach ([
                            'Judul' => $judul,
                            'Alat' => $riwayat->formAlat->id_alat.($riwayat->formAlat->alat->model ? ', '.$riwayat->formAlat->alat->model : ''),
                            'Petugas' => $riwayat->pengguna?->nama ?? '-',
                            'Waktu' => $riwayat->dicek_pada?->format('d M Y, H:i') ?? '-',
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
                            <dd class="mt-0.5 text-sm text-ink">{{ $riwayat->catatan ?? '-' }}</dd>
                        </div>
                        @if ($gambar)
                            <div>
                                <dt class="text-xs text-muted">Gambar</dt>
                                <dd class="mt-2"><img src="{{ asset('storage/'.$gambar) }}" alt="Gambar {{ $judul }}" class="h-47.5 w-full rounded-lg border border-line object-cover"></dd>
                            </div>
                        @endif
                    </dl>

                    <x-button :href="route('admin.alat.show', $riwayat->formAlat->id_alat)" class="mt-9 h-11 w-full shrink-0">Buka Detail Alat</x-button>
                </aside>
            </dialog>
        @endforeach
    </x-slot:modals>
</x-layouts.admin>
