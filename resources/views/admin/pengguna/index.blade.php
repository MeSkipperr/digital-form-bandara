@php
    // Sample data from the Figma design. Replace with real queries when the backend is wired up.
    $daftarPengguna = [
        ['id' => 'yoga001', 'inisial' => 'YP', 'nama' => 'Yoga Pratama', 'peran' => 'Petugas', 'aktif' => true, 'warna' => '#12345A'],
        ['id' => 'made002', 'inisial' => 'MD', 'nama' => 'Made Dwi Astuti', 'peran' => 'Petugas', 'aktif' => true, 'warna' => '#1D4E89'],
        ['id' => 'komang003', 'inisial' => 'KA', 'nama' => 'Komang Ayu Lestari', 'peran' => 'Supervisor', 'aktif' => true, 'warna' => '#19A7A0'],
        ['id' => 'putu004', 'inisial' => 'PA', 'nama' => 'Putu Arya Wijaya', 'peran' => 'Petugas', 'aktif' => false, 'warna' => '#475569'],
        ['id' => 'kadek005', 'inisial' => 'KS', 'nama' => 'Kadek Surya Dharma', 'peran' => 'Petugas', 'aktif' => true, 'warna' => '#0F766E'],
        ['id' => 'wayan006', 'inisial' => 'WS', 'nama' => 'Wayan Sari Dewi', 'peran' => 'Admin', 'aktif' => true, 'warna' => '#334155'],
    ];

@endphp

<x-layouts.admin title="Kelola Pengguna" active="pengguna">
    <div class="flex items-center gap-5">
        <x-search-input placeholder="Cari nama atau nama pengguna" class="w-95" />
        <x-button class="w-45">
            <x-icon name="tambah" class="size-4" />
            Tambah Pengguna
        </x-button>
    </div>

    <x-card class="mt-5 overflow-hidden">
        <table class="w-full">
            <thead class="border-b border-line-soft">
                <tr>
                    <x-table-heading class="w-20">Foto</x-table-heading>
                    <x-table-heading class="px-0">Nama</x-table-heading>
                    <x-table-heading class="w-55 px-0">Peran</x-table-heading>
                    <x-table-heading class="w-52 px-0">Status</x-table-heading>
                    <x-table-heading class="text-right">Aksi</x-table-heading>
                </tr>
            </thead>
            <tbody class="divide-y divide-line-soft">
                @foreach ($daftarPengguna as $pengguna)
                    <tr class="h-16 hover:bg-hover/60">
                        <td class="px-6">
                            <x-avatar :initials="$pengguna['inisial']" :color="$pengguna['warna']" class="size-10 text-sm" />
                        </td>
                        <td>
                            <p class="text-sm text-ink">{{ $pengguna['nama'] }}</p>
                            <p class="text-xs text-muted">{{ '@'.$pengguna['id'] }}</p>
                        </td>
                        <td class="text-sm text-ink">{{ $pengguna['peran'] }}</td>
                        <td>
                            <x-badge :color="$pengguna['aktif'] ? '#16A34A' : '#64748B'" :label="$pengguna['aktif'] ? 'Aktif' : 'Nonaktif'" />
                        </td>
                        <td class="px-6 text-right">
                            <x-button variant="secondary" class="h-9 w-18" data-modal-open="modal-ubah-pengguna-{{ $pengguna['id'] }}">Ubah</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-6 flex items-center justify-between pr-9">
        <p class="text-xs text-muted">Menampilkan {{ count($daftarPengguna) }} dari 24 pengguna</p>
        <nav class="flex items-center gap-2 text-sm" aria-label="Halaman">
            @foreach ([1, 2, 3] as $halaman)
                <a
                    href="{{ request()->fullUrlWithQuery(['halaman' => $halaman]) }}"
                    @class([
                        'flex h-7 w-9 items-center justify-center rounded-md border',
                        'border-line bg-line text-primary' => $halaman === (int) request()->query('halaman', 1),
                        'border-line bg-white text-muted hover:bg-hover' => $halaman !== (int) request()->query('halaman', 1),
                    ])
                >{{ $halaman }}</a>
            @endforeach
        </nav>
    </div>

    <x-slot:modals>
        @foreach ($daftarPengguna as $pengguna)
            <x-modal id="modal-ubah-pengguna-{{ $pengguna['id'] }}" title="Ubah Pengguna">
                <form method="dialog" class="flex flex-col">
                    <div class="flex flex-col gap-4">
                        <x-form.input label="Nama" name="nama" :value="$pengguna['nama']" />
                        <x-form.select label="Peran" name="peran" :options="['Petugas' => 'Petugas', 'Supervisor' => 'Supervisor', 'Admin' => 'Admin']" :selected="$pengguna['peran']" />
                    </div>

                    <div class="mt-7 flex items-center justify-between border-b border-line-soft pb-6">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-sm text-ink">Status aktif</span>
                            <span class="text-xs text-muted">Pengguna nonaktif tidak bisa masuk</span>
                        </div>
                        <x-form.toggle name="aktif" :checked="$pengguna['aktif']" aria-label="Status aktif" />
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex flex-col gap-1">
                            <span class="text-base font-bold text-ink">Ubah Kata Sandi</span>
                            <span class="text-xs text-muted">Gunakan jika pengguna lupa kata sandi</span>
                        </div>
                        <x-button variant="secondary" class="h-9 w-37.5">Ubah Kata Sandi</x-button>
                    </div>

                    <div class="mt-4">
                        <x-form.input label="Kata sandi baru" name="kata_sandi" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" />
                    </div>

                    <div class="mt-10 flex items-center justify-end gap-3">
                        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
                        <x-button type="submit" class="w-30">Simpan</x-button>
                    </div>
                </form>
            </x-modal>
        @endforeach
    </x-slot:modals>
</x-layouts.admin>
