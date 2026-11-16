{{--
    Tambah / Ubah Alat form from the "02-tambah-alat" design.
    Status is not editable here: it is fixed to the `bagus` / `perlu_perbaikan` enum and follows the pengecekan results.
    The hidden `_form` key tells which modal was submitted: after a validation error only that form gets the old
    input and the error messages, and app.js reopens its modal ([data-buka-ulang]).

    @var \App\Models\Alat|null $alat
    @var \Illuminate\Support\Collection<int, \App\Models\KategoriAlat> $daftarKategori
--}}
@php
    $alat ??= null;
    $kunciForm = $alat ? 'ubah-alat-'.$alat->id : 'tambah-alat';
    $formAktif = old('_form') === $kunciForm;
    $galat = fn (string $kolom): ?string => $formAktif ? $errors->first($kolom) : null;
@endphp

<form
    method="post"
    action="{{ $alat ? route('admin.alat.update', $alat) : route('admin.alat.store') }}"
    class="flex flex-col gap-4"
    @if ($formAktif && $errors->any()) data-buka-ulang @endif
>
    @csrf
    @if ($alat)
        @method('put')
    @endif
    <input type="hidden" name="_form" value="{{ $kunciForm }}">

    <div class="grid grid-cols-2 gap-4">
        <x-form.input
            label="ID Alat"
            name="id"
            :value="$alat?->id ?? ($formAktif ? old('id') : null)"
            :error="$galat('id')"
            placeholder="XRAY-07"
            maxlength="20"
            pattern="[A-Za-z0-9_\-]+"
            title="Huruf, angka, tanda - dan _ tanpa spasi"
            autocomplete="off"
            mono
            required
            :readonly="$alat !== null"
        />
        <x-form.input
            label="Model"
            name="model"
            :value="$formAktif ? old('model') : $alat?->model"
            :error="$galat('model')"
            placeholder="Xray Pro Max"
            maxlength="100"
            autocomplete="off"
        />
    </div>

    <div>
        <x-form.label>Kategori</x-form.label>
        @include('admin.alat.partials.daftar-kategori', [
            'dapatDipilih' => true,
            'kategoriTerpilih' => $formAktif ? old('id_kategori_alat') : $alat?->id_kategori_alat,
        ])
        @if ($pesan = $galat('id_kategori_alat'))
            <p class="mt-1.5 text-xs text-danger">{{ $pesan }}</p>
        @endif
    </div>

    <x-form.input
        label="Lokasi"
        name="lokasi"
        :value="$formAktif ? old('lokasi') : $alat?->lokasi"
        :error="$galat('lokasi')"
        placeholder="Gudang Belakang"
        maxlength="100"
        autocomplete="off"
    />

    <div class="mt-4 flex items-center justify-end gap-3">
        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
        <x-button type="submit" class="w-25">Simpan</x-button>
    </div>
</form>
