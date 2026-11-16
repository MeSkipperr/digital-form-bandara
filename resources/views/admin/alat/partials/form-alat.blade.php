{{--
    Tambah / Ubah Alat form from the "02-tambah-alat" design.
    Status is not editable here: it is fixed to the `bagus` / `perlu_perbaikan` enum and follows the pengecekan results.

    @var \App\Models\Alat|null $alat
    @var \Illuminate\Support\Collection<int, \App\Models\KategoriAlat> $daftarKategori
--}}
@php
    $alat ??= null;
@endphp

<form method="post" action="{{ $alat ? route('admin.alat.update', $alat) : route('admin.alat.store') }}" class="flex flex-col gap-4">
    @csrf
    @if ($alat)
        @method('put')
    @endif

    <div class="grid grid-cols-2 gap-4">
        <x-form.input label="ID Alat" name="id" :value="$alat?->id" placeholder="XRAY-07" maxlength="20" mono required :readonly="$alat !== null" />
        <x-form.input label="Model" name="model" :value="$alat?->model" placeholder="Xray Pro Max" maxlength="100" />
    </div>

    <div>
        <x-form.label>Kategori</x-form.label>
        @include('admin.alat.partials.daftar-kategori', ['dapatDipilih' => true, 'kategoriTerpilih' => $alat?->id_kategori_alat])
    </div>

    <x-form.input label="Lokasi" name="lokasi" :value="$alat?->lokasi" placeholder="Gudang Belakang" maxlength="100" />

    <div class="mt-4 flex items-center justify-end gap-3">
        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
        <x-button type="submit" class="w-25">Simpan</x-button>
    </div>
</form>
