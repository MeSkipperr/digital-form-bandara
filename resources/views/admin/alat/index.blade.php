@php
    $contoh = require resource_path('views/admin/alat/partials/contoh-data.php');
    $daftarStatus = $contoh['status'];
    $daftarKategori = $contoh['kategori'];

    $kataKunci = mb_strtolower(trim((string) request()->query('cari')));
    $kategoriAktif = request()->query('kategori');
    $statusAktif = request()->query('status');

    $daftarAlat = array_filter(
        $contoh['alat'],
        fn (array $alat, string $id): bool => ($kataKunci === '' || str_contains(mb_strtolower($id.' '.$alat['model'].' '.$alat['lokasi']), $kataKunci))
            && (blank($kategoriAktif) || (string) $alat['id_kategori_alat'] === (string) $kategoriAktif)
            && (blank($statusAktif) || $alat['status'] === $statusAktif),
        ARRAY_FILTER_USE_BOTH,
    );
@endphp

<x-layouts.admin title="Kelola Alat" active="alat">
    <form method="get" class="flex items-center gap-3" data-auto-submit>
        <x-search-input placeholder="Cari ID, model, lokasi" class="mr-2 w-75" />
        <x-form.select name="kategori" :options="['' => 'Semua kategori'] + $daftarKategori" :selected="$kategoriAktif" surface="bg-white" class="w-45" aria-label="Filter kategori" />
        <x-form.select name="status" :options="['' => 'Semua status'] + array_map(fn (array $status): string => $status['label'], $daftarStatus)" :selected="$statusAktif" surface="bg-white" class="w-42.5" aria-label="Filter status" />

        <x-button class="ml-auto w-40" data-modal-open="modal-tambah-alat">
            <x-icon name="tambah" class="size-4" />
            Tambah Alat
        </x-button>
    </form>

    <x-card class="mt-5">
        <table class="w-full">
            <thead class="border-b border-line-soft">
                <tr>
                    <x-table-heading class="w-36">ID</x-table-heading>
                    <x-table-heading class="w-55 px-0">Model</x-table-heading>
                    <x-table-heading class="w-45 px-0">Kategori</x-table-heading>
                    <x-table-heading class="w-45 px-0">Status</x-table-heading>
                    <x-table-heading class="px-0">Lokasi</x-table-heading>
                    <x-table-heading class="w-20"><span class="sr-only">Aksi</span></x-table-heading>
                </tr>
            </thead>
            <tbody class="divide-y divide-line-soft">
                @forelse ($daftarAlat as $id => $alat)
                    <tr class="h-15 hover:bg-hover/60">
                        <td class="px-6 font-mono text-sm font-bold text-ink">{{ $id }}</td>
                        <td class="text-sm text-ink">{{ $alat['model'] }}</td>
                        <td class="text-sm text-ink">{{ $daftarKategori[$alat['id_kategori_alat']] }}</td>
                        <td><x-badge :color="$daftarStatus[$alat['status']]['warna']" :label="$daftarStatus[$alat['status']]['label']" /></td>
                        <td class="text-sm text-ink">{{ $alat['lokasi'] }}</td>
                        <td class="px-6 text-right">
                            <x-action-menu>
                                <x-action-menu.item data-modal-open="modal-ubah-alat-{{ $id }}">Ubah</x-action-menu.item>
                                <x-action-menu.item danger>Hapus</x-action-menu.item>
                                <x-action-menu.item :href="route('admin.alat.show', $id)">Detail</x-action-menu.item>
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
    </x-card>

    <x-slot:modals>
        <x-modal id="modal-tambah-alat" title="Tambah Alat">
            @include('admin.alat.partials.form-alat', ['daftarKategori' => $daftarKategori])
        </x-modal>

        @foreach ($daftarAlat as $id => $alat)
            <x-modal id="modal-ubah-alat-{{ $id }}" title="Ubah Alat">
                @include('admin.alat.partials.form-alat', ['idAlat' => $id, 'alat' => $alat, 'daftarKategori' => $daftarKategori])
            </x-modal>
        @endforeach

        @include('admin.alat.partials.modal-tambah-kategori')
    </x-slot:modals>
</x-layouts.admin>
