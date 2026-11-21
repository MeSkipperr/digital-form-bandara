<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\FormAlat;
use App\Models\RiwayatPengecekan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    public function scan()
    {
        return view('petugas.scan');
    }

    public function cekAlat($kode_alat)
    {
        $kode = trim($kode_alat);

        $alat = Alat::with(['kategoriAlat', 'formAlat.templateForm'])
            ->find($kode);

        if (! $alat) {
            return redirect()->route('petugas.scan')
                ->with('error', "Alat dengan ID '$kode' tidak ditemukan di sistem.");
        }

        $daftarForm = $alat->formAlat;

        if ($daftarForm->isEmpty()) {
            return redirect()->route('petugas.scan')
                ->with('error', "Alat '$kode' belum memiliki template form pengecekan.");
        }

        // Jika alat punya > 1 form (misal X-RAY), tampilkan pilihan bagiannya dulu
        if ($daftarForm->count() > 1) {
            return view('petugas.pilih-bagian', compact('alat', 'daftarForm'));
        }

        // Jika cuma 1 form (ETD, WTMD, HHMD), langsung masuk ke 1 file form
        return redirect()->route('petugas.form', $daftarForm->first()->id);
    }

    public function tampilForm(FormAlat $formAlat)
    {
        abort_unless($formAlat->masihTerpasang(), 404);

        $formAlat->load(['alat.kategoriAlat', 'templateForm.tugas']);

        $jumlahBagian = $formAlat->alat->formAlat()->count();

        return view('petugas.form-pengecekan', [
            'formAlat' => $formAlat,
            'alat' => $formAlat->alat,
            'template' => $formAlat->templateForm,
            'daftarTugas' => $formAlat->templateForm->tugas,
            'jumlahBagian' => $jumlahBagian,
        ]);
    }

    public function simpanForm(Request $request, FormAlat $formAlat)
    {
        abort_unless($formAlat->masihTerpasang(), 404);

        $formAlat->load(['alat', 'templateForm.tugas']);
        $daftarTugas = $formAlat->templateForm->tugas;

        $rules = [
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
        foreach ($daftarTugas as $tugas) {
            $rules['jawaban.'.$tugas->id] = ['required', 'in:bagus,kurang_bagus'];
        }

        $validated = $request->validate($rules, [
            'jawaban.*.required' => 'Semua poin pengecekan wajib diisi.',
        ]);

        $adaYangKurang = in_array('kurang_bagus', $validated['jawaban'] ?? [], true);
        $statusHasil = $adaYangKurang ? 'kurang_bagus' : 'bagus';

        // Catat poin yang kurang bagus agar terbaca jelas di halaman riwayat Admin
        $catatanUser = trim((string) ($validated['catatan'] ?? ''));
        $poinBermasalah = [];
        foreach ($daftarTugas as $tugas) {
            if (($validated['jawaban'][$tugas->id] ?? '') === 'kurang_bagus') {
                $poinBermasalah[] = '- '.$tugas->nama.' (Kurang Bagus)';
            }
        }

        $catatanFinal = $catatanUser;
        if (! empty($poinBermasalah)) {
            $ringkasan = "Temuan:\n".implode("\n", $poinBermasalah);
            $catatanFinal = $catatanUser !== ''
                ? $catatanUser."\n\n".$ringkasan
                : $ringkasan;
        }

        DB::transaction(function () use ($formAlat, $statusHasil, $catatanFinal, $adaYangKurang) {
            $riwayat = RiwayatPengecekan::where('id_form_alat', $formAlat->id)
                ->where('status', 'tertunda')
                ->latest('id')
                ->first();

            $dataSimpan = [
                'id_form_alat' => $formAlat->id,
                'id_pengguna' => Auth::id(),
                'catatan' => $catatanFinal !== '' ? $catatanFinal : null,
                'status' => $statusHasil,
                'dicek_pada' => now(),
            ];

            if ($riwayat) {
                $riwayat->update($dataSimpan);
            } else {
                RiwayatPengecekan::create($dataSimpan);
            }

            $formAlat->alat->update([
                'status' => $adaYangKurang ? 'perlu_perbaikan' : 'bagus',
            ]);
        });

        $jumlahBagian = $formAlat->alat->formAlat()->count();
        if ($jumlahBagian > 1) {
            return redirect()->route('petugas.cek', $formAlat->id_alat)
                ->with('success', 'Checklist '.$formAlat->templateForm->nama.' berhasil disimpan!');
        }

        return redirect()->route('petugas.scan')
            ->with('success', 'Checklist '.$formAlat->id_alat.' berhasil disimpan!');
    }
}
