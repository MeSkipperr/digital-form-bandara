<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PetugasController extends Controller
{
    // Menampilkan halaman Scan QR
    public function scan()
    {
        return view('petugas.scan');
    }

    // Menerima hasil scan QR dan mengarahkan ke form yang sesuai
    public function cekAlat($kode_alat)
    {
        $kode = strtoupper(trim($kode_alat));

        // Khusus X-RAY masuk ke halaman pilihan 2 bagian dulu
        if (str_starts_with($kode, 'X-RAY') || str_starts_with($kode, 'XRAY')) {
            return view('petugas.xray-pilih', ['kode_alat' => $kode]);
        }

        // Alat lainnya langsung ke form masing-masing
        if (str_starts_with($kode, 'ETD')) {
            return view('petugas.forms.form-etd', ['kode_alat' => $kode]);
        }
        if (str_starts_with($kode, 'WTMD')) {
            return view('petugas.forms.form-wtmd', ['kode_alat' => $kode]);
        }
        if (str_starts_with($kode, 'HHMD')) {
            return view('petugas.forms.form-hhmd', ['kode_alat' => $kode]);
        }

        return redirect()->route('petugas.scan')
            ->with('error', "Kode QR '$kode' tidak dikenali sistem.");
    }
}