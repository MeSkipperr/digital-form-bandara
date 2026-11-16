<x-layouts.admin title="Pengguna" active="pengguna">
    <form method="get" class="flex items-center gap-3" data-auto-submit>
        <x-search-input placeholder="Cari nama atau username" class="w-72" />
        <x-button class="ml-auto w-44" data-modal-open="modal-tambah-pengguna">
            <x-icon name="tambah" class="size-4" />
            Tambah Pengguna
        </x-button>
    </form>

    <x-alert-messages class="mt-4" />

    <x-card class="mt-4">
        <table class="w-full">
            <thead class="border-b border-line-soft">
                <tr>
                    <x-table-heading class="w-80">Pengguna</x-table-heading>
                    <x-table-heading class="w-52 px-0">Username</x-table-heading>
                    <x-table-heading class="w-44 px-0">Peran</x-table-heading>
                    <x-table-heading class="px-0">Status Akun</x-table-heading>
                    <x-table-heading class="w-20 text-right">Aksi</x-table-heading>
                </tr>
            </thead>
            <tbody class="divide-y divide-line-soft">
                @forelse ($daftarPengguna as $pengguna)
                    <tr class="h-13 hover:bg-hover/60">
                        <td class="px-6">
                            <div class="flex items-center gap-3">
                                <x-avatar :initials="$pengguna->inisial()" :color="$pengguna->warnaAvatar()" class="size-8 text-xs" />
                                <span class="text-sm font-bold text-ink">{{ $pengguna->nama }}</span>
                            </div>
                        </td>
                        <td class="text-sm text-ink">{{ '@'.$pengguna->id }}</td>
                        <td class="text-sm text-ink">{{ $daftarPeran[$pengguna->peran] }}</td>
                        <td>
                            <x-badge :color="$pengguna->aktif ? '#16A34A' : '#64748B'" :label="$pengguna->aktif ? 'Aktif' : 'Nonaktif'" />
                        </td>
                        <td class="px-6 text-right">
                            <x-action-menu>
                                <x-action-menu.item data-modal-open="modal-ubah-pengguna-{{ $pengguna->id }}">Ubah</x-action-menu.item>
                            </x-action-menu>
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

    <x-pagination :paginator="$daftarPengguna" label="pengguna" class="mt-4" />

    <x-slot:modals>
        <x-modal id="modal-tambah-pengguna" title="Tambah Pengguna" description="Atur identitas dan peran pengguna aplikasi.">
            @include('admin.pengguna.partials.form-pengguna', ['pengguna' => null])
        </x-modal>

        @foreach ($daftarPengguna as $pengguna)
            <x-modal id="modal-ubah-pengguna-{{ $pengguna->id }}" title="Ubah Pengguna" description="Atur identitas dan peran pengguna aplikasi.">
                @include('admin.pengguna.partials.form-pengguna', ['pengguna' => $pengguna])
            </x-modal>
        @endforeach
    </x-slot:modals>
</x-layouts.admin>
