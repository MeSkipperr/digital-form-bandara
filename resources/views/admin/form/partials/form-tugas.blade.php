{{--
    Tambah / Ubah Task modal body for the Kelola Tugas page.

    @var \App\Models\TemplateForm $templateForm
    @var \App\Models\Tugas|null $tugas
--}}
@php
    $tugas ??= null;
@endphp

<form
    method="post"
    action="{{ $tugas ? route('admin.form.tugas.update', [$templateForm, $tugas]) : route('admin.form.tugas.store', $templateForm) }}"
    class="flex flex-col gap-4"
>
    @csrf
    @if ($tugas)
        @method('put')
    @endif

    <x-form.input label="Nama Task" name="nama" :value="$tugas?->nama" placeholder="Contoh: Periksa kabel daya" maxlength="100" required />

    <div class="mt-4 flex items-center justify-end gap-3">
        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
        <x-button type="submit" class="w-25">Simpan</x-button>
    </div>
</form>
