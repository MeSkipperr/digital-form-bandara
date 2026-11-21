<x-layouts.admin title="Alat" active="alat">
    <form method="get" class="grid grid-cols-2 items-center gap-3 sm:grid-cols-4 xl:flex" data-auto-submit>
        <x-search-input placeholder="Cari ID, model, atau lokasi" class="col-span-2 xl:mr-2 xl:w-75" />
        <x-form.select name="kategori" :options="['' => 'Semua kategori'] + $daftarKategori->pluck('nama', 'id')->all()" :selected="$kategoriAktif" surface="bg-white" class="w-45" aria-label="Filter kategori" />
        <x-form.select name="status" :options="['' => 'Semua status'] + array_map(fn (array $status): string => $status['label'], $daftarStatus)" :selected="$statusAktif" surface="bg-white" class="w-42.5" aria-label="Filter status" />

        <x-button variant="secondary" class="font-bold sm:col-start-3 xl:ml-auto xl:w-40" data-modal-open="modal-kelola-kategori">
            <x-icon name="label" class="size-4" />
            Kelola Kategori
        </x-button>
        <x-button class="xl:w-45" data-modal-open="modal-tambah-alat">
            <x-icon name="tambah" class="size-4" />
            Tambah Alat
        </x-button>
    </form>

    <x-alert-messages class="mt-4" :form-dengan-galat-sendiri="['tambah-alat', 'ubah-alat-*']" />

    <x-card class="mt-5">
        <x-table-scroll>
            <table class="w-full min-w-5xl xl:min-w-0">
                <thead class="border-b border-line-soft">
                    <tr>
                        <x-table-heading class="w-36">ID</x-table-heading>
                        <x-table-heading class="w-55 px-0">Model</x-table-heading>
                        <x-table-heading class="w-40 px-0">Kategori</x-table-heading>
                        <x-table-heading class="w-45 px-0">Status</x-table-heading>
                        <x-table-heading class="px-0">Lokasi</x-table-heading>
                        <x-table-heading class="w-20 text-right">Aksi</x-table-heading>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line-soft">
                    @forelse ($daftarAlat as $alat)
                        @php($status = $daftarStatus[$alat->status])
                        <tr class="h-15 hover:bg-hover/60">
                            <td class="px-6 font-mono text-sm font-bold text-ink">{{ $alat->id }}</td>
                            <td class="text-sm text-ink">{{ $alat->model }}</td>
                            <td class="text-sm text-ink">{{ $alat->kategoriAlat->nama }}</td>
                            <td><x-badge :color="$status['warna']" :label="$status['label']" /></td>
                            <td class="text-sm text-ink">{{ $alat->lokasi }}</td>
                            <td class="px-6 text-right">
                                <x-action-menu>
                                    <x-action-menu.item :href="route('admin.alat.show', $alat)">Detail</x-action-menu.item>
                                    <x-action-menu.item data-modal-open="modal-ubah-alat-{{ $alat->id }}">Ubah</x-action-menu.item>
                                    <x-action-menu.item danger data-modal-open="modal-hapus-alat-{{ $alat->id }}">Hapus</x-action-menu.item>
                                </x-action-menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-muted">Tidak ada alat yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-scroll>
    </x-card>

    <x-pagination :paginator="$daftarAlat" label="alat" class="mt-4" />

    <x-slot:modals>
        <x-modal id="modal-tambah-alat" title="Tambah Alat">
            @include('admin.alat.partials.form-alat', ['alat' => null])
        </x-modal>

        @foreach ($daftarAlat as $alat)
            <x-modal id="modal-ubah-alat-{{ $alat->id }}" title="Ubah Alat">
                @include('admin.alat.partials.form-alat', ['alat' => $alat])
            </x-modal>
            <x-modal id="modal-hapus-alat-{{ $alat->id }}" title="Hapus Alat" description="Alat {{ $alat->id }} akan dihapus permanen.">
                @if ($alat->riwayat_tercatat_count > 0)
                    <x-danger-alert>Alat {{ $alat->id }} sudah memiliki {{ $alat->riwayat_tercatat_count }} riwayat pengecekan. Riwayat tetap tersimpan, tetapi data alatnya akan kosong.</x-danger-alert>
                @endif

                <form method="post" action="{{ route('admin.alat.destroy', $alat) }}" class="mt-4 flex items-center justify-end gap-3">
                    @csrf
                    @method('delete')
                    <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
                    <x-button type="submit" variant="danger" class="w-30">Hapus</x-button>
                </form>
            </x-modal>
        @endforeach

        @include('admin.alat.partials.modal-kelola-kategori')
    </x-slot:modals>
</x-layouts.admin>
