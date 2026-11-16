<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriAlat;
use App\Models\RiwayatPengecekan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RiwayatPengecekanController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci = trim((string) $request->query('cari'));
        $kategoriAktif = $request->query('kategori');
        $statusAktif = $request->query('status');

        $daftarRiwayat = RiwayatPengecekan::query()
            ->with(['formAlat.alat', 'formAlat.templateForm'])
            ->when($kataKunci !== '', function ($query) use ($kataKunci): void {
                $query->where(function ($query) use ($kataKunci): void {
                    $query->where('id_pengguna', 'like', "%{$kataKunci}%")
                        ->orWhereHas('formAlat.templateForm', fn ($query) => $query->where('nama', 'like', "%{$kataKunci}%"))
                        ->orWhereHas('formAlat.alat', function ($query) use ($kataKunci): void {
                            $query->where('id', 'like', "%{$kataKunci}%")
                                ->orWhere('model', 'like', "%{$kataKunci}%");
                        });
                });
            })
            ->when(filled($kategoriAktif), fn ($query) => $query->whereHas('formAlat.alat', fn ($query) => $query->where('id_kategori_alat', $kategoriAktif)))
            ->when(array_key_exists((string) $statusAktif, RiwayatPengecekan::DAFTAR_STATUS), fn ($query) => $query->where('status', $statusAktif))
            ->orderByDesc('dicek_pada')
            ->orderByDesc('id')
            ->paginate(self::JUMLAH_PER_HALAMAN, pageName: 'halaman')
            ->withQueryString();

        return view('admin.riwayat.index', [
            'daftarRiwayat' => $daftarRiwayat,
            'daftarStatus' => RiwayatPengecekan::DAFTAR_STATUS,
            'daftarKategori' => KategoriAlat::query()->orderBy('id')->pluck('nama', 'id')->all(),
            'kategoriAktif' => $kategoriAktif,
            'statusAktif' => $statusAktif,
        ]);
    }
}
