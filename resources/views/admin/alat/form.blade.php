{{--
    "Kelola Form" of one alat: the template forms assigned to it. "Tambah Form" assigns unassigned template forms of the
    alat's kategori; new template forms are created on the Form page. A form with filled riwayat cannot be removed.

    @var \App\Models\Alat $alat
    @var \Illuminate\Support\Collection<int, \App\Models\FormAlat> $daftarFormAlat  with templateForm (tugas_count) and riwayat_tercatat_count
    @var \Illuminate\Support\Collection<int, \App\Models\TemplateForm> $daftarFormTersedia  with tugas_count
--}}
@php
    $namaKategori = $alat->kategoriAlat->nama;
    $urlKelolaForm = route('admin.form.index', ['kategori' => $alat->id_kategori_alat]);
    $terpilih = array_map('intval', (array) old('id_template_form', []));
@endphp

<x-layouts.admin title="Kelola Form" active="alat" breadcrumb="Inspeksi Alat / Alat / Form">
    <x-back-button :href="route('admin.alat.show', $alat)" />

    <h2 class="mt-7.5 text-xl font-bold text-ink">Pilih Form</h2>
    <p class="mt-1.5 text-[13px] text-muted">{{ $alat->id }}{{ $alat->model ? ', '.$alat->model : '' }} · Kategori {{ $namaKategori }}</p>

    <x-alert-messages class="mt-4" />

    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:gap-7">
        @foreach ($daftarFormAlat as $formAlat)
            @php($urlGambar = $formAlat->templateForm->urlGambar())
            <x-card class="flex flex-col p-4">
                <div class="flex aspect-video items-center justify-center overflow-hidden rounded-md bg-primary-dark text-sidebar-icon xl:aspect-auto xl:h-30">
                    @if ($urlGambar)
                        <x-zoomable-image :src="$urlGambar" alt="Gambar {{ $formAlat->templateForm->nama }}" class="size-full object-contain xl:object-cover" />
                    @else
                        <x-icon name="form" class="size-8" />
                    @endif
                </div>
                <p class="mt-4.5 text-sm font-bold text-ink">{{ $formAlat->templateForm->nama }}</p>
                <p class="mt-1 text-xs text-muted">{{ $formAlat->templateForm->tugas_count }} task</p>
                <div class="mt-auto flex items-center gap-2 pt-1.5">
                    <x-button variant="secondary" :href="route('admin.alat.form.task', [$alat, $formAlat])" class="h-9 flex-1">Lihat</x-button>
                    <x-icon-button icon="hapus" variant="danger" data-modal-open="modal-lepas-form-{{ $formAlat->id }}" aria-label="Lepas {{ $formAlat->templateForm->nama }}" title="Lepas dari alat" />
                </div>
            </x-card>
        @endforeach

        <button type="button" class="flex min-h-40 flex-col items-center sm:min-h-62.5 justify-center gap-4 rounded-xl border border-dashed border-line bg-white hover:bg-hover/50" data-modal-open="modal-tambah-form">
            <span class="flex size-14 items-center justify-center rounded-full bg-line text-primary">
                <x-icon name="tambah" class="size-6" />
            </span>
            <span class="text-sm font-bold text-primary">Tambah Form</span>
        </button>
    </div>

    <x-slot:modals>
        <x-modal id="modal-tambah-form" title="Tambah Form" description="Pilih form kategori {{ $namaKategori }} yang akan diperiksa pada alat ini.">
            @if ($daftarFormTersedia->isEmpty())
                <p class="rounded-lg border border-dashed border-line px-4 py-6 text-center text-sm text-muted">
                    {{ $daftarFormAlat->isEmpty() ? "Belum ada form untuk kategori {$namaKategori}." : "Semua form kategori {$namaKategori} sudah dipasang." }}
                    Buat form baru di halaman Form.
                </p>

                <div class="mt-8 flex items-center justify-end gap-3">
                    <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
                    <x-button :href="$urlKelolaForm" class="w-40">Buka Halaman Form</x-button>
                </div>
            @else
                <form method="post" action="{{ route('admin.alat.form.store', $alat) }}" class="flex flex-col gap-4" @if ($errors->has('id_template_form*')) data-buka-ulang @endif>
                    @csrf

                    <fieldset class="flex max-h-80 flex-col gap-2 overflow-y-auto">
                        <legend class="sr-only">Form yang dipasang</legend>
                        @foreach ($daftarFormTersedia as $templateForm)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-3.5 py-3 hover:bg-hover has-checked:border-primary has-checked:bg-primary/5">
                                <input type="checkbox" name="id_template_form[]" value="{{ $templateForm->id }}" class="size-4.5 shrink-0 accent-primary" @checked(in_array($templateForm->id, $terpilih, true))>
                                <span class="flex min-w-0 flex-1 flex-col">
                                    <span class="truncate text-sm font-bold text-ink">{{ $templateForm->nama }}</span>
                                    <span class="text-xs text-muted">{{ $templateForm->tugas_count }} task</span>
                                </span>
                            </label>
                        @endforeach
                    </fieldset>

                    @if ($errors->has('id_template_form*'))
                        <p class="text-xs text-danger">{{ $errors->first('id_template_form*') }}</p>
                    @endif

                    <a href="{{ $urlKelolaForm }}" class="text-xs font-bold text-primary hover:underline">Form belum ada? Buat di halaman Form</a>

                    <div class="mt-4 flex items-center justify-end gap-3">
                        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
                        <x-button type="submit" class="w-30">Simpan</x-button>
                    </div>
                </form>
            @endif
        </x-modal>

        @foreach ($daftarFormAlat as $formAlat)
            <x-modal id="modal-lepas-form-{{ $formAlat->id }}" title="Lepas Form" description="Form tetap tersedia di halaman Form dan dapat dipasang lagi.">
                @if ($formAlat->riwayat_tercatat_count > 0)
                    <x-danger-alert>{{ $formAlat->templateForm->nama }} sudah memiliki {{ $formAlat->riwayat_tercatat_count }} riwayat pengecekan di alat ini, sehingga tidak dapat dilepas agar riwayatnya tetap tersimpan.</x-danger-alert>
                @else
                    <x-danger-alert>{{ $formAlat->templateForm->nama }} akan dilepas dari alat {{ $alat->id }} beserta pengecekan yang masih tertunda.</x-danger-alert>
                @endif

                <form method="post" action="{{ route('admin.alat.form.destroy', [$alat, $formAlat]) }}" class="mt-8 flex items-center justify-end gap-3">
                    @csrf
                    @method('delete')
                    <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
                    <x-button type="submit" variant="danger" class="w-33 disabled:cursor-not-allowed disabled:opacity-50" :disabled="$formAlat->riwayat_tercatat_count > 0">Lepas Form</x-button>
                </form>
            </x-modal>
        @endforeach
    </x-slot:modals>
</x-layouts.admin>
