@php
    $idAlat = $alat->id;
@endphp

<x-layouts.admin title="Kelola Task" active="alat">
    <x-back-button :href="route('admin.alat.form.index', $idAlat)" />

    <div class="mt-7 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-ink">{{ $form['nama'] }}</h2>
            <p class="mt-1 text-[13px] text-muted">{{ $idAlat }}, {{ count($daftarTask) }} task</p>
        </div>
        <x-icon-button data-modal-open="modal-tambah-task" aria-label="Tambah task" />
    </div>

    <x-card class="mt-8">
        <ul class="divide-y divide-line-soft">
            @foreach ($daftarTask as $task)
                <li class="flex h-20 items-center gap-5 px-6">
                    <input type="checkbox" name="task[]" class="size-6 shrink-0 appearance-none rounded-md border border-line bg-canvas checked:border-primary checked:bg-primary" aria-label="Pilih {{ $task['nama'] }}">
                    <div class="flex flex-1 flex-col gap-1">
                        <span class="text-sm font-bold text-ink">{{ $task['nama'] }}</span>
                        <span class="text-xs text-muted">{{ $task['catatan'] }}</span>
                    </div>
                    <x-button variant="secondary" class="h-9 w-18">Ubah</x-button>
                </li>
            @endforeach
        </ul>
    </x-card>

    <x-slot:modals>
        <x-modal id="modal-tambah-task" title="Tambah Task">
            <form method="dialog" class="flex flex-col gap-4">
                <x-form.input label="Nama" name="nama" placeholder="Contoh: Periksa kabel daya" autofocus />
                <x-form.textarea label="Catatan" name="catatan" placeholder="Jelaskan langkah pengecekan" />

                <div class="mt-4 flex items-center justify-end gap-3">
                    <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
                    <x-button type="submit" class="w-30">Simpan</x-button>
                </div>
            </form>
        </x-modal>
    </x-slot:modals>
</x-layouts.admin>
