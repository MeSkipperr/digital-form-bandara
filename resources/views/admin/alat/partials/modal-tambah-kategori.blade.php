{{-- "Tambah Kategori" popup, opened from the "+ Tambah Kategori" item of the Kategori dropdown in the alat form. --}}
<x-modal id="modal-tambah-kategori" title="Tambah Kategori" description="Kategori baru langsung bisa dipilih di form alat.">
    <form method="dialog" class="flex flex-col" data-tambah-kategori>
        <x-form.input label="Nama Kategori" name="nama" placeholder="Metal Detector" maxlength="100" required />

        <div class="mt-8 flex items-center justify-end gap-3">
            <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
            <x-button type="submit" class="w-30">Simpan</x-button>
        </div>
    </form>

    <template data-template-opsi-kategori>
        @include('admin.alat.partials.opsi-kategori', ['idKategori' => '', 'namaKategori' => '', 'terpilih' => false])
    </template>
</x-modal>
