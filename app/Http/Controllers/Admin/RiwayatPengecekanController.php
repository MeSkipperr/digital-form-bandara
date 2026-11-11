<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\RiwayatPengecekan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RiwayatPengecekanController extends Controller
{
    /**
     * Period filter options: query value => number of days back (null means no limit).
     *
     * @var array<string, array{label: string, hari: int|null}>
     */
    protected const DAFTAR_PERIODE = [
        '' => ['label' => 'Semua waktu', 'hari' => null],
        '7' => ['label' => '7 hari terakhir', 'hari' => 7],
        '30' => ['label' => '30 hari terakhir', 'hari' => 30],
    ];

    public function index(Request $request): View
    {
        $kataKunci = trim((string) $request->query('cari'));
        $statusAktif = $request->query('status');
        $alatAktif = $request->query('alat');
        $periodeAktif = (string) $request->query('periode', '');
        $hari = self::DAFTAR_PERIODE[$periodeAktif]['hari'] ?? null;

        $daftarRiwayat = RiwayatPengecekan::query()
            ->with(['pengguna', 'formAlat.alat', 'formAlat.templateForm'])
            ->when($kataKunci !== '', function ($query) use ($kataKunci): void {
                $query->where(function ($query) use ($kataKunci): void {
                    $query->where('catatan', 'like', "%{$kataKunci}%")
                        ->orWhereHas('formAlat.templateForm', fn ($query) => $query->where('nama', 'like', "%{$kataKunci}%"));
                });
            })
            ->when(array_key_exists((string) $statusAktif, RiwayatPengecekan::DAFTAR_STATUS), fn ($query) => $query->where('status', $statusAktif))
            ->when(filled($alatAktif), fn ($query) => $query->whereHas('formAlat', fn ($query) => $query->where('id_alat', $alatAktif)))
            ->when($hari !== null, fn ($query) => $query->where('dicek_pada', '>=', now()->subDays($hari)))
            ->orderByDesc('dicek_pada')
            ->orderByDesc('id')
            ->paginate(10, pageName: 'halaman')
            ->withQueryString();

        return view('admin.riwayat.index', [
            'daftarRiwayat' => $daftarRiwayat,
            'daftarStatus' => RiwayatPengecekan::DAFTAR_STATUS,
            'daftarPeriode' => array_map(fn (array $periode): string => $periode['label'], self::DAFTAR_PERIODE),
            'daftarAlat' => Alat::query()->orderBy('id')->pluck('id', 'id')->all(),
        ]);
    }
}
