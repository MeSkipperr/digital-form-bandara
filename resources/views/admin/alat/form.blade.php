@php
    $idAlat = $alat->id;
@endphp

<x-layouts.admin title="Kelola Form" active="alat">
    <x-back-button :href="route('admin.alat.show', $idAlat)" />

    <h2 class="mt-7.5 text-xl font-bold text-ink">Pilih Form</h2>
    <p class="mt-1.5 text-[13px] text-muted">{{ $idAlat }}, {{ $alat['model'] }}</p>

    <div class="mt-5 grid grid-cols-3 gap-7">
        @foreach ($daftarForm as $idForm => $form)
            <x-card class="p-4">
                <img src="{{ asset($form['gambar']) }}" alt="Gambar {{ $form['nama'] }}" class="h-30 w-full rounded-md object-cover">
                <p class="mt-4.5 text-sm font-bold text-ink">{{ $form['nama'] }}</p>
                <p class="mt-1 text-xs text-muted">{{ $form['jumlah_task'] }} task</p>
                <x-button variant="secondary" :href="route('admin.alat.form.task', [$idAlat, $idForm])" class="mt-1.5 h-9 w-full">Lihat</x-button>
            </x-card>
        @endforeach

        <button type="button" class="flex min-h-62.5 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-line bg-white hover:bg-hover/50" data-modal-open="modal-tambah-form">
            <span class="flex size-14 items-center justify-center rounded-full bg-line text-primary">
                <x-icon name="tambah" class="size-6" />
            </span>
            <span class="text-sm font-bold text-primary">Tambah Form</span>
        </button>
    </div>

    <x-slot:modals>
        <x-modal id="modal-tambah-form" title="Tambah Form">
            <form method="dialog" class="flex flex-col gap-4">
                <x-form.input label="Nama Form" name="nama" placeholder="Contoh: Form Xray Atas" autofocus />

                <label class="block">
                    <x-form.label>Unggah Gambar</x-form.label>
                    <span class="flex h-27.5 cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-line bg-canvas hover:border-primary/40">
                        <x-icon name="tambah" class="size-6 text-primary" />
                        <span class="mt-2.5 text-sm text-ink">Tarik gambar ke sini atau pilih file</span>
                        <span class="mt-1 text-xs text-muted">PNG atau JPG, maksimal 5 MB</span>
                    </span>
                    <input type="file" name="gambar" accept="image/png,image/jpeg" class="sr-only">
                </label>

                <x-form.textarea label="Catatan" name="catatan" placeholder="Tulis catatan untuk petugas" />

                <div class="mt-4 flex items-center justify-end gap-3">
                    <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
                    <x-button type="submit" class="w-30">Simpan</x-button>
                </div>
            </form>
        </x-modal>
    </x-slot:modals>
</x-layouts.admin>
