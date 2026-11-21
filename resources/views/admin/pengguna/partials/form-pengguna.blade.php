{{--
    Tambah / Ubah Pengguna form from the "02c-modal-edit-pengguna" design, without the email field.
    The username is the primary key referenced by riwayat and sesi; changing it cascades to them.
    The hidden `_form` key tells which modal was submitted: after a validation error only that form gets the old
    input and the error messages, and app.js reopens its modal ([data-buka-ulang]). The password is never refilled.

    @var \App\Models\Pengguna|null $pengguna
    @var array<string, string> $daftarPeran
--}}
@php
    $pengguna ??= null;
    $kunciForm = $pengguna ? 'ubah-pengguna-'.$pengguna->id : 'tambah-pengguna';
    $formAktif = old('_form') === $kunciForm;
    $galat = fn (string $kolom): ?string => $formAktif ? $errors->first($kolom) : null;
@endphp

<form
    method="post"
    action="{{ $pengguna ? route('admin.pengguna.update', $pengguna) : route('admin.pengguna.store') }}"
    class="flex flex-col gap-4"
    @if ($formAktif && $errors->any()) data-buka-ulang @endif
    @if ($pengguna)
        data-nilai-asli="{{ json_encode(['nama' => $pengguna->nama, 'id' => $pengguna->id, 'kata_sandi' => '', 'peran' => $pengguna->peran, 'aktif' => (int) $pengguna->aktif]) }}"
    @else
        data-nilai-awal="{{ json_encode(['nama' => '', 'id' => '', 'kata_sandi' => '', 'peran' => array_key_first($daftarPeran), 'aktif' => 1]) }}"
    @endif
>
    @csrf
    @if ($pengguna)
        @method('put')
    @endif
    <input type="hidden" name="_form" value="{{ $kunciForm }}">

    <x-form.input
        label="Nama Lengkap"
        name="nama"
        :value="$formAktif ? old('nama') : $pengguna?->nama"
        :error="$galat('nama')"
        placeholder="Yoga Pratama"
        maxlength="100"
        autocomplete="off"
        required
    />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-form.input
            label="Username"
            name="id"
            :value="$formAktif ? old('id') : $pengguna?->id"
            :error="$galat('id')"
            placeholder="yoga001"
            maxlength="20"
            pattern="[A-Za-z0-9_\-]+"
            title="Huruf, angka, tanda - dan _ tanpa spasi"
            autocomplete="off"
            required
        />
        <x-form.input
            :label="$pengguna ? 'Kata Sandi Baru' : 'Kata Sandi'"
            name="kata_sandi"
            type="password"
            :error="$galat('kata_sandi')"
            :placeholder="$pengguna ? 'Kosongkan jika tetap' : 'Minimal 8 karakter'"
            minlength="8"
            autocomplete="new-password"
            :required="$pengguna === null"
        />
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-form.select
            label="Peran"
            name="peran"
            :options="$daftarPeran"
            :selected="$formAktif ? old('peran') : $pengguna?->peran"
            :error="$galat('peran')"
            surface="bg-white"
            required
        />
        <x-form.select
            label="Status Akun"
            name="aktif"
            :options="['1' => 'Aktif', '0' => 'Nonaktif']"
            :selected="$formAktif ? old('aktif') : (int) ($pengguna?->aktif ?? true)"
            :error="$galat('aktif')"
            surface="bg-white"
        />
    </div>

    <div class="mt-6 flex items-center justify-end gap-3">
        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
        <x-button type="submit" class="w-37.5">Simpan Pengguna</x-button>
    </div>
</form>
