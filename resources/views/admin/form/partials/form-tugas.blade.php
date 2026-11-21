{{--
    Tambah / Ubah Task modal body for the Kelola Tugas page.
    The hidden `_form` key tells which modal was submitted: after a validation error only that form gets the old
    input and the error message, and app.js reopens its modal ([data-buka-ulang]).

    @var \App\Models\TemplateForm $templateForm
    @var \App\Models\Tugas|null $tugas
--}}
@php
    $tugas ??= null;
    $kunciForm = $tugas ? 'ubah-tugas-'.$tugas->id : 'tambah-tugas';
    $formAktif = old('_form') === $kunciForm;
@endphp

<form
    method="post"
    action="{{ $tugas ? route('admin.form.tugas.update', [$templateForm, $tugas]) : route('admin.form.tugas.store', $templateForm) }}"
    class="flex flex-col gap-4"
    @if ($formAktif && $errors->any()) data-buka-ulang @endif
    @if ($tugas)
        data-nilai-asli="{{ json_encode(['nama' => $tugas->nama]) }}"
    @endif
>
    @csrf
    @if ($tugas)
        @method('put')
    @endif
    <input type="hidden" name="_form" value="{{ $kunciForm }}">

    <x-form.input
        label="Nama Task"
        name="nama"
        :value="$formAktif ? old('nama') : $tugas?->nama"
        :error="$formAktif ? $errors->first('nama') : null"
        placeholder="Contoh: Periksa kabel daya"
        maxlength="100"
        autocomplete="off"
        required
    />

    <div class="mt-4 flex items-center justify-end gap-3">
        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
        <x-button type="submit" class="w-25">Simpan</x-button>
    </div>
</form>
