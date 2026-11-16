{{-- "05-kelola-form" design: template form cards with tambah, ubah and hapus modals. --}}
<x-layouts.admin title="Kelola Form" breadcrumb="Inspeksi Alat / Form" active="form">
    <h2 class="text-xl font-bold text-ink">Pilih Form</h2>
    <p class="mt-1.5 text-[13px] text-muted">{{ $daftarForm->total() }} form tersedia</p>

    <form method="get" class="mt-5 flex items-center gap-3" data-auto-submit>
        <x-form.select name="kategori" :options="['' => 'Semua kategori'] + $daftarKategori->pluck('nama', 'id')->all()" :selected="$kategoriAktif" surface="bg-white" class="w-45" aria-label="Filter kategori" />

        <x-button variant="secondary" class="ml-auto w-40 font-bold" data-modal-open="modal-kelola-kategori">
            <x-icon name="label" class="size-4" />
            Kelola Kategori
        </x-button>
    </form>

    <x-alert-messages class="mt-4" />

    <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($daftarForm as $form)
            @php($urlGambar = $form->urlGambar())
            <x-card class="flex flex-col p-4">
                <div class="flex h-30 items-center justify-center overflow-hidden rounded-md bg-primary-dark text-sidebar-icon">
                    @if ($urlGambar)
                        <img src="{{ $urlGambar }}" alt="Gambar {{ $form->nama }}" class="size-full object-cover">
                    @else
                        <x-icon name="form" class="size-8" />
                    @endif
                </div>

                <p class="mt-4 text-sm font-bold text-ink">{{ $form->nama }}</p>
                <p class="mt-1 text-xs text-muted uppercase">{{ $form->kategoriAlat->nama }}</p>

                <div class="mt-auto flex items-center gap-2 pt-4">
                    <x-button variant="secondary" :href="route('admin.form.tugas.index', $form)" class="h-9 flex-1">Detail</x-button>
                    <x-icon-button icon="pensil" variant="secondary" data-modal-open="modal-ubah-form-{{ $form->id }}" aria-label="Ubah {{ $form->nama }}" title="Ubah" />
                    <x-icon-button icon="hapus" variant="danger" data-modal-open="modal-hapus-form-{{ $form->id }}" aria-label="Hapus {{ $form->nama }}" title="Hapus" />
                </div>
            </x-card>
        @empty
            <x-card class="flex items-center justify-center px-6 py-10 text-center text-sm text-muted xl:col-span-2">
                Tidak ada form untuk kategori ini.
            </x-card>
        @endforelse

        <button
            type="button"
            class="flex min-h-58 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-line bg-white/60 transition-colors hover:border-primary/40 hover:bg-white"
            data-modal-open="modal-tambah-form"
        >
            <span class="flex size-11 items-center justify-center rounded-full bg-primary text-white">
                <x-icon name="tambah" class="size-5" />
            </span>
            <span class="text-sm font-bold text-primary">Tambah Form</span>
        </button>
    </div>

    <x-pagination :paginator="$daftarForm" label="form" class="mt-5" />

    <x-slot:modals>
        <x-modal id="modal-tambah-form" title="Tambah Form">
            @include('admin.form.partials.form-template', ['templateForm' => null])
        </x-modal>

        @foreach ($daftarForm as $form)
            <x-modal id="modal-ubah-form-{{ $form->id }}" title="Ubah Form">
                @include('admin.form.partials.form-template', ['templateForm' => $form])
            </x-modal>

            <x-modal id="modal-hapus-form-{{ $form->id }}" title="Hapus Form" description="Tindakan ini tidak dapat dibatalkan.">
                @if ($form->form_alat_count > 0)
                    <x-danger-alert>{{ $form->nama }} masih dipakai {{ $form->form_alat_count }} alat. Lepaskan form dari alat tersebut sebelum menghapusnya.</x-danger-alert>
                @else
                    <x-danger-alert>{{ $form->nama }} akan dihapus beserta {{ $form->tugas_count }} task di dalamnya.</x-danger-alert>
                @endif

                <form method="post" action="{{ route('admin.form.destroy', $form) }}" class="mt-8 flex items-center justify-end gap-3">
                    @csrf
                    @method('delete')
                    <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
                    <x-button type="submit" variant="danger" class="w-33 disabled:cursor-not-allowed disabled:opacity-50" :disabled="$form->form_alat_count > 0">Hapus Form</x-button>
                </form>
            </x-modal>
        @endforeach

        @include('admin.alat.partials.modal-kelola-kategori')
    </x-slot:modals>
</x-layouts.admin>
