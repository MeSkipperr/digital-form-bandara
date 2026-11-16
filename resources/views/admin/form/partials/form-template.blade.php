{{--
    Tambah / Ubah Form modal body from the "05b-modal-tambah-form" and "05c-modal-ubah-form" designs.
    The hidden `_form` key tells which modal was submitted: after a validation error only that form gets the old
    input and the error messages, and app.js reopens its modal ([data-buka-ulang]). A picked image cannot be refilled.

    @var \App\Models\TemplateForm|null $templateForm
    @var \Illuminate\Support\Collection<int, \App\Models\KategoriAlat> $daftarKategori
    @var string|null $kategoriAktif  preselected kategori for a new form
--}}
@php
    $templateForm ??= null;
    $kunciForm = $templateForm ? 'ubah-form-'.$templateForm->id : 'tambah-form';
    $formAktif = old('_form') === $kunciForm;
    $galat = fn (string $kolom): ?string => $formAktif ? $errors->first($kolom) : null;
@endphp

<form
    method="post"
    action="{{ $templateForm ? route('admin.form.update', $templateForm) : route('admin.form.store') }}"
    enctype="multipart/form-data"
    class="flex flex-col gap-4"
    @if ($formAktif && $errors->any()) data-buka-ulang @endif
>
    @csrf
    @if ($templateForm)
        @method('put')
    @endif
    <input type="hidden" name="_form" value="{{ $kunciForm }}">

    <x-form.input
        label="Nama Form"
        name="nama"
        :value="$formAktif ? old('nama') : $templateForm?->nama"
        :error="$galat('nama')"
        placeholder="Contoh: Form Xray Atas"
        maxlength="100"
        autocomplete="off"
        required
    />

    <x-form.select
        label="Kategori"
        name="id_kategori_alat"
        :options="['' => 'Pilih kategori'] + $daftarKategori->pluck('nama', 'id')->all()"
        :selected="$formAktif ? old('id_kategori_alat') : ($templateForm?->id_kategori_alat ?? $kategoriAktif)"
        :error="$galat('id_kategori_alat')"
        surface="bg-white"
        required
    />

    @include('admin.form.partials.unggah-gambar', [
        'idInput' => 'gambar-form-'.($templateForm?->id ?? 'baru'),
        'urlGambar' => $templateForm?->urlGambar(),
        'pesanGalat' => $galat('gambar'),
    ])

    <div class="mt-4 flex items-center justify-end gap-3">
        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
        <x-button type="submit" class="w-25">Simpan</x-button>
    </div>
</form>
