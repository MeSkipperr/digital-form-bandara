{{-- "Kelola Kategori" popup, opened from the Alat page toolbar and from "Buka Kelola Kategori" in the Kategori dropdown. --}}
<x-modal id="modal-kelola-kategori" title="Kelola Kategori" description="Ubah nama, tambah, atau hapus kategori alat. Perubahan langsung tersimpan.">
    <div class="rounded-lg border border-line">
        @include('admin.alat.partials.daftar-kategori', ['dapatDipilih' => false])
    </div>

    <x-slot:footer>
        <x-button variant="secondary" class="w-25" data-modal-close>Selesai</x-button>
    </x-slot:footer>
</x-modal>
