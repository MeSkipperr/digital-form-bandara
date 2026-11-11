<?php

namespace App\Console\Commands;

use App\Models\FormAlat;
use App\Models\RiwayatPengecekan;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('riwayat-pengecekan:generate')]
#[Description('Buat riwayat pengecekan berstatus tertunda untuk setiap form alat')]
class GenerateRiwayatPengecekanHarian extends Command
{
    /**
     * Number of form_alat rows processed per bulk insert.
     */
    protected const UKURAN_CHUNK = 500;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $log = Log::channel('riwayat_pengecekan');
        $waktuMulai = microtime(true);

        $log->info('Generate riwayat_pengecekan dimulai', ['waktu' => now()->toDateTimeString()]);

        try {
            $jumlahDibuat = 0;

            FormAlat::query()
                ->select('id')
                ->chunkById(self::UKURAN_CHUNK, function (Collection $daftarFormAlat) use (&$jumlahDibuat): void {
                    RiwayatPengecekan::query()->insert(
                        $daftarFormAlat->map(fn (FormAlat $formAlat): array => [
                            'id_form_alat' => $formAlat->id,
                            'id_pengguna' => null,
                            'catatan' => null,
                            'status' => 'tertunda',
                            'dicek_pada' => null,
                        ])->all()
                    );

                    $jumlahDibuat += $daftarFormAlat->count();
                });
        } catch (Throwable $exception) {
            $log->error('Generate riwayat_pengecekan gagal', ['pesan' => $exception->getMessage()]);
            report($exception);

            return self::FAILURE;
        }

        $log->info('Generate riwayat_pengecekan selesai', [
            'jumlah' => $jumlahDibuat,
            'durasi_ms' => (int) round((microtime(true) - $waktuMulai) * 1000),
        ]);

        $this->info("{$jumlahDibuat} riwayat pengecekan tertunda dibuat.");

        return self::SUCCESS;
    }
}
