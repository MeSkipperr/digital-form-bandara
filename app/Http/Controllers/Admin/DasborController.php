<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Pengguna;
use App\Models\RiwayatPengecekan;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class DasborController extends Controller
{
    public function __invoke(Request $request): View
    {
        $periode = $request->query('periode') === '30' ? '30' : '7';
        $formPerHari = $this->formPerHari((int) $periode);

        return view('admin.dasbor', [
            'statusAlat' => $this->statusAlat(),
            'formHariIni' => $this->formHariIni(),
            'formPerHari' => $formPerHari['titik'],
            'batasGrafik' => $formPerHari['batas'],
            'langkahGrafik' => $formPerHari['langkah'],
            'titikSorot' => $formPerHari['sorot'],
            'penggunaAktif' => $this->penggunaAktif(),
            'periode' => $periode,
        ]);
    }

    /**
     * @return list<array{label: string, value: int, color: string}>
     */
    protected function statusAlat(): array
    {
        $jumlah = Alat::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return collect(['perlu_perbaikan', 'bagus'])
            ->map(fn (string $status): array => [
                'label' => Alat::DAFTAR_STATUS[$status]['label'],
                'value' => (int) ($jumlah[$status] ?? 0),
                'color' => Alat::DAFTAR_STATUS[$status]['warna'],
            ])
            ->all();
    }

    /**
     * Pending inspections are not dated, so "Menunggu" counts every pending row;
     * the other statuses count inspections done today.
     *
     * @return list<array{label: string, value: int, color: string}>
     */
    protected function formHariIni(): array
    {
        $hariIni = RiwayatPengecekan::query()
            ->whereBetween('dicek_pada', [now()->startOfDay(), now()->endOfDay()])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $jumlah = [
            'tertunda' => RiwayatPengecekan::query()->where('status', 'tertunda')->count(),
            'bagus' => $hariIni['bagus'] ?? 0,
            'kurang_bagus' => $hariIni['kurang_bagus'] ?? 0,
            'terlewati' => $hariIni['terlewati'] ?? 0,
        ];

        return collect($jumlah)
            ->map(fn (int $total, string $status): array => [
                'label' => ['bagus' => 'Baik', 'kurang_bagus' => 'Buruk'][$status] ?? RiwayatPengecekan::DAFTAR_STATUS[$status]['label'],
                'value' => $total,
                'color' => RiwayatPengecekan::DAFTAR_STATUS[$status]['warna'],
            ])
            ->values()
            ->all();
    }

    /**
     * Forms filled (bagus or kurang_bagus) per day over the last $hari days, today last.
     *
     * @return array{titik: list<array{label: string, value: int, tooltip: string}>, batas: int, langkah: int, sorot: int}
     */
    protected function formPerHari(int $hari): array
    {
        $hariIni = CarbonImmutable::now()->startOfDay();
        $mulai = $hariIni->subDays($hari - 1);

        $jumlahPerTanggal = RiwayatPengecekan::query()
            ->whereIn('status', ['bagus', 'kurang_bagus'])
            ->whereBetween('dicek_pada', [$mulai, $hariIni->endOfDay()])
            ->pluck('dicek_pada')
            ->countBy(fn ($waktu): string => $waktu->toDateString());

        $titik = [];

        for ($i = 0; $i < $hari; $i++) {
            $tanggal = $mulai->addDays($i)->locale('id');

            $titik[] = [
                'label' => $hari > 7 ? (string) $tanggal->day : $tanggal->isoFormat('ddd'),
                'value' => (int) ($jumlahPerTanggal[$tanggal->toDateString()] ?? 0),
                'tooltip' => $tanggal->isoFormat('dddd, D MMM'),
            ];
        }

        $nilaiTertinggi = max(array_column($titik, 'value'));
        $batas = max(10, (int) ceil($nilaiTertinggi / 10) * 10);
        $langkah = 10;

        if ($batas > 50) {
            $batas = (int) ceil($nilaiTertinggi / 50) * 50;
            $langkah = intdiv($batas, 5);
        }

        return [
            'titik' => $titik,
            'batas' => $batas,
            'langkah' => $langkah,
            'sorot' => (int) array_search($nilaiTertinggi, array_column($titik, 'value'), true),
        ];
    }

    /**
     * Active users that have an unexpired session.
     *
     * @return Collection<int, Pengguna>
     */
    protected function penggunaAktif(): Collection
    {
        return Pengguna::query()
            ->where('aktif', true)
            ->whereHas('sesiPengguna', fn ($query) => $query->where('aktif_sampai', '>', now()))
            ->orderBy('nama')
            ->limit(5)
            ->get();
    }
}
