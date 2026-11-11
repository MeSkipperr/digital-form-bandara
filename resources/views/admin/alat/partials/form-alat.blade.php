{{--
    Tambah / Ubah Alat form, matching the `alat` table columns.
    Status is not editable here: it is fixed to the `bagus` / `perlu_perbaikan` enum and follows the pengecekan results.
--}}
@php
    $idAlat ??= null;
    $alat ??= null;
    $kategoriTerpilih = $alat['id_kategori_alat'] ?? null;
@endphp

<form method="dialog" class="flex flex-col gap-4">
    <div class="grid grid-cols-2 gap-6">
        <x-form.input label="ID Alat" name="id" :value="$idAlat" placeholder="XRAY-04" maxlength="20" mono required :readonly="$idAlat !== null" />
        <x-form.input label="Model" name="model" :value="$alat['model'] ?? null" placeholder="Xray Pro Max" maxlength="100" />
    </div>

    <div>
        <x-form.label>Kategori</x-form.label>
        <details data-dropdown data-select-badge>
            <summary class="flex h-10 cursor-pointer items-center justify-between rounded-lg border border-line bg-canvas px-3.5 text-sm text-ink">
                <span data-select-display>
                    @if ($kategoriTerpilih !== null)
                        <span>{{ $daftarKategori[$kategoriTerpilih] }}</span>
                    @else
                        <span class="text-muted">Pilih kategori</span>
                    @endif
                </span>
                <x-icon name="chevron-bawah" class="size-4.5 text-muted" />
            </summary>
            <div class="mt-1.5 rounded-lg border border-line bg-white">
                <div class="flex max-h-44 flex-col gap-1 overflow-y-auto px-1.5 py-2" role="listbox" data-kategori-options>
                    @foreach ($daftarKategori as $idKategori => $namaKategori)
                        @include('admin.alat.partials.opsi-kategori', ['idKategori' => $idKategori, 'namaKategori' => $namaKategori, 'terpilih' => $idKategori === $kategoriTerpilih])
                    @endforeach
                </div>
                <button type="button" class="flex w-full items-center gap-2 border-t border-line px-4 py-3 text-sm font-bold text-primary-dark hover:bg-hover" data-modal-open="modal-tambah-kategori" data-kategori-asal>
                    <x-icon name="tambah" class="size-4 text-primary" />
                    Tambah Kategori
                </button>
            </div>
        </details>
    </div>

    <x-form.input label="Lokasi" name="lokasi" :value="$alat['lokasi'] ?? null" placeholder="Gudang Belakang" maxlength="100" />

    <x-form.textarea label="Deskripsi" name="deskripsi" placeholder="Keterangan singkat tentang alat">{{ $alat['deskripsi'] ?? '' }}</x-form.textarea>

    <div class="mt-4 flex items-center justify-end gap-3">
        <x-button variant="secondary" class="w-25" data-modal-close>Batal</x-button>
        <x-button type="submit" class="w-30">Simpan</x-button>
    </div>
</form>
