<x-layouts.admin title="Kelola Pengguna" active="pengguna">
    <form method="get" class="flex items-center gap-5" data-auto-submit>
        <x-search-input placeholder="Cari nama atau nama pengguna" class="w-95" />
        <x-button type="button" class="w-45" data-modal-open="modal-tambah-pengguna">
            <x-icon name="tambah" class="size-4" />
            Tambah Pengguna
        </x-button>
    </form>

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
                @forelse ($daftarPengguna as $pengguna)
                    <tr class="h-16 hover:bg-hover/60">
                        <td class="px-6">
                            <x-avatar :initials="$pengguna->inisial()" :color="$pengguna->warnaAvatar()" class="size-10 text-sm" />
                        </td>
                        <td>
                            <p class="text-sm text-ink">{{ $pengguna->nama }}</p>
                            <p class="text-xs text-muted">{{ '@'.$pengguna->id }}</p>
                        </td>
                        <td class="text-sm text-ink">{{ $daftarPeran[$pengguna->peran] }}</td>
                        <td>
                            <x-badge :color="$pengguna->aktif ? '#16A34A' : '#64748B'" :label="$pengguna->aktif ? 'Aktif' : 'Nonaktif'" />
                        </td>
                        <td class="px-6 text-right">
                            <x-button variant="secondary" class="h-9 w-18" data-modal-open="modal-ubah-pengguna-{{ $pengguna->id }}">Ubah</x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-muted">Tidak ada pengguna yang cocok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <div class="mt-6 flex items-center justify-between pr-9">
        <p class="text-xs text-muted">Menampilkan {{ $daftarPengguna->count() }} dari {{ $daftarPengguna->total() }} pengguna</p>
        <nav class="flex items-center gap-2 text-sm" aria-label="Halaman">
            @foreach (range(1, $daftarPengguna->lastPage()) as $halaman)
                <a
                    href="{{ $daftarPengguna->url($halaman) }}"
                    @class([
                        'flex h-7 w-9 items-center justify-center rounded-md border',
                        'border-line bg-line text-primary' => $halaman === $daftarPengguna->currentPage(),
                        'border-line bg-white text-muted hover:bg-hover' => $halaman !== $daftarPengguna->currentPage(),
                    ])
                >{{ $halaman }}</a>
            @endforeach
        </nav>
    </div>

    <x-slot:modals>
        @foreach ($daftarPengguna as $pengguna)
            <x-modal id="modal-ubah-pengguna-{{ $pengguna->id }}" title="Ubah Pengguna">
                <form method="post" action="{{ route('admin.pengguna.update', $pengguna) }}" class="flex flex-col">
                    @csrf
                    @method('put')
                    <div class="flex flex-col gap-4">
                        <x-form.input label="Nama" name="nama" :value="$pengguna->nama" maxlength="100" required />
                        <x-form.select label="Peran" name="peran" :options="$daftarPeran" :selected="$pengguna->peran" />
                    </div>

                    <div class="mt-7 flex items-center justify-between border-b border-line-soft pb-6">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-sm text-ink">Status aktif</span>
                            <span class="text-xs text-muted">Pengguna nonaktif tidak bisa masuk</span>
                        </div>
                        <input type="hidden" name="aktif" value="0">
                        <x-form.toggle name="aktif" value="1" :checked="$pengguna->aktif" aria-label="Status aktif" />
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex flex-col gap-1">
                            <span class="text-base font-bold text-ink">Ubah Kata Sandi</span>
                            <span class="text-xs text-muted">Gunakan jika pengguna lupa kata sandi</span>
                        </div>
                        <x-button type="button" variant="secondary" class="h-9 w-37.5">Ubah Kata Sandi</x-button>
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

        <x-modal id="modal-tambah-pengguna" title="Tambah Pengguna">
            <form method="post" action="{{ route('admin.pengguna.store') }}" class="flex flex-col gap-4">
                @csrf
                <x-form.input label="Nama pengguna (ID)" name="id" placeholder="yoga001" maxlength="20" mono required />
                <x-form.input label="Nama" name="nama" placeholder="Yoga Pratama" maxlength="100" required />
                <x-form.select label="Peran" name="peran" :options="$daftarPeran" />
                <x-form.input label="Kata sandi" name="kata_sandi" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required />

                <div class="mt-4 flex items-center justify-end gap-3">
                    <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
                    <x-button type="submit" class="w-30">Simpan</x-button>
                </div>
            </form>
        </x-modal>
    </x-slot:modals>
</x-layouts.admin>
