{{--
    Tambah / Ubah Form modal body from the "05b-modal-tambah-form" and "05c-modal-ubah-form" designs.

    @var \App\Models\TemplateForm|null $templateForm
    @var \Illuminate\Support\Collection<int, \App\Models\KategoriAlat> $daftarKategori
    @var string|null $kategoriAktif  preselected kategori for a new form
--}}
@php
    $templateForm ??= null;
@endphp

<form
    method="post"
    action="{{ $templateForm ? route('admin.form.update', $templateForm) : route('admin.form.store') }}"
    enctype="multipart/form-data"
    class="flex flex-col gap-4"
>
    @csrf
    @if ($templateForm)
        @method('put')
    @endif

    <x-form.input label="Nama Form" name="nama" :value="$templateForm?->nama" placeholder="Contoh: Form Xray Atas" maxlength="100" required />

    <x-form.select
        label="Kategori"
        name="id_kategori_alat"
        :options="$daftarKategori->pluck('nama', 'id')->all()"
        :selected="$templateForm?->id_kategori_alat ?? $kategoriAktif"
        surface="bg-white"
        required
    />

    @include('admin.form.partials.unggah-gambar', [
        'idInput' => 'gambar-form-'.($templateForm?->id ?? 'baru'),
        'urlGambar' => $templateForm?->urlGambar(),
    ])

    <div class="mt-4 flex items-center justify-end gap-3">
        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
        <x-button type="submit" class="w-25">Simpan</x-button>
    </div>
</form>
