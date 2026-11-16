{{--
    Tambah / Ubah Pengguna form from the "02c-modal-edit-pengguna" design, without the email field.
    The username is the primary key referenced by riwayat and sesi, so it is read-only when editing.

    @var \App\Models\Pengguna|null $pengguna
    @var array<string, string> $daftarPeran
--}}
@php
    $pengguna ??= null;
@endphp

<form method="post" action="{{ $pengguna ? route('admin.pengguna.update', $pengguna) : route('admin.pengguna.store') }}" class="flex flex-col gap-4">
    @csrf
    @if ($pengguna)
        @method('put')
    @endif

    <x-form.input label="Nama Lengkap" name="nama" :value="$pengguna?->nama" placeholder="Yoga Pratama" maxlength="100" required />

    <div class="grid grid-cols-2 gap-4">
        <x-form.input label="Username" name="id" :value="$pengguna?->id" placeholder="yoga001" maxlength="20" required :readonly="$pengguna !== null" />
        <x-form.input
            :label="$pengguna ? 'Kata Sandi Baru' : 'Kata Sandi'"
            name="kata_sandi"
            type="password"
            :placeholder="$pengguna ? 'Kosongkan jika tetap' : 'Minimal 8 karakter'"
            minlength="8"
            autocomplete="new-password"
            :required="$pengguna === null"
        />
    </div>

    <div class="grid grid-cols-2 gap-4">
        <x-form.select label="Peran" name="peran" :options="$daftarPeran" :selected="$pengguna?->peran" surface="bg-white" />
        <x-form.select label="Status Akun" name="aktif" :options="['1' => 'Aktif', '0' => 'Nonaktif']" :selected="(int) ($pengguna?->aktif ?? true)" surface="bg-white" />
    </div>

    <div class="mt-6 flex items-center justify-end gap-3">
        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
        <x-button type="submit" class="w-37.5">Simpan Pengguna</x-button>
    </div>
</form>
