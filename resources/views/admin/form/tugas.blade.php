{{-- "06-kelola-tugas" design: the tasks of one template form with tambah, ubah and hapus modals. --}}
<x-layouts.admin title="Kelola Tugas" breadcrumb="Inspeksi Alat / Form / Tugas" active="form">
    <x-back-button :href="route('admin.form.index')">Kembali ke Kelola Form</x-back-button>

    <div class="mt-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-xl font-bold text-ink sm:text-2xl">{{ $templateForm->nama }}</h2>
        <x-button class="w-full sm:w-40" data-modal-open="modal-tambah-tugas">
            <x-icon name="tambah" class="size-4" />
            Tambah Task
        </x-button>
    </div>

    <x-alert-messages class="mt-4" />

    <x-card class="mt-6">
        <ul class="divide-y divide-line-soft">
            @forelse ($daftarTugas as $tugas)
                <li class="flex h-15 items-center gap-3 px-4 sm:gap-4 sm:px-5">
                    <span class="size-5 shrink-0 rounded-md border border-line bg-canvas" aria-hidden="true"></span>
                    <span class="min-w-0 flex-1 truncate text-sm text-ink">{{ $tugas->nama }}</span>
                    <x-button variant="secondary" class="h-9 w-16 px-0" data-modal-open="modal-ubah-tugas-{{ $tugas->id }}">Ubah</x-button>
                    <x-icon-button icon="hapus" variant="danger" data-modal-open="modal-hapus-tugas-{{ $tugas->id }}" aria-label="Hapus {{ $tugas->nama }}" title="Hapus" />
                </li>
            @empty
                <li class="px-6 py-10 text-center text-sm text-muted">Belum ada task di form ini.</li>
            @endforelse
        </ul>
    </x-card>

    <x-slot:modals>
        <x-modal id="modal-tambah-tugas" title="Tambah Task">
            @include('admin.form.partials.form-tugas', ['tugas' => null])
        </x-modal>

        @foreach ($daftarTugas as $tugas)
            <x-modal id="modal-ubah-tugas-{{ $tugas->id }}" title="Ubah Task">
                @include('admin.form.partials.form-tugas', ['tugas' => $tugas])
            </x-modal>

            <x-modal id="modal-hapus-tugas-{{ $tugas->id }}" title="Hapus Task" description="Tindakan ini tidak dapat dibatalkan.">
                <x-danger-alert>Task {{ $tugas->nama }} akan dihapus dari form ini.</x-danger-alert>

                <form method="post" action="{{ route('admin.form.tugas.destroy', [$templateForm, $tugas]) }}" class="mt-8 flex items-center justify-end gap-3">
                    @csrf
                    @method('delete')
                    <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
                    <x-button type="submit" variant="danger" class="w-33">Hapus Task</x-button>
                </form>
            </x-modal>
        @endforeach
    </x-slot:modals>
</x-layouts.admin>
