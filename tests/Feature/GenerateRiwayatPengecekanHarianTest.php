<?php

namespace Tests\Feature;

use App\Models\FormAlat;
use App\Models\RiwayatPengecekan;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class GenerateRiwayatPengecekanHarianTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_one_pending_riwayat_for_every_form_alat(): void
    {
        $daftarFormAlat = FormAlat::factory()->count(3)->create();

        $this->artisan('riwayat-pengecekan:generate')->assertSuccessful();

        $this->assertDatabaseCount('riwayat_pengecekan', 3);

        foreach ($daftarFormAlat as $formAlat) {
            $this->assertDatabaseHas('riwayat_pengecekan', [
                'id_form_alat' => $formAlat->id,
                'id_pengguna' => null,
                'catatan' => null,
                'status' => 'tertunda',
                'dicek_pada' => null,
            ]);
        }
    }

    public function test_each_run_adds_a_new_set_of_pending_riwayat(): void
    {
        $formAlat = FormAlat::factory()->create();

        $this->artisan('riwayat-pengecekan:generate')->assertSuccessful();
        $this->artisan('riwayat-pengecekan:generate')->assertSuccessful();

        $this->assertSame(2, RiwayatPengecekan::query()->where('id_form_alat', $formAlat->id)->count());
    }

    public function test_it_logs_the_start_and_finish_of_each_run(): void
    {
        FormAlat::factory()->count(2)->create();

        $logger = Mockery::spy(LoggerInterface::class);
        Log::shouldReceive('channel')->with('riwayat_pengecekan')->andReturn($logger);

        $this->artisan('riwayat-pengecekan:generate')->assertSuccessful();

        $logger->shouldHaveReceived('info')
            ->with('Generate riwayat_pengecekan dimulai', Mockery::on(fn (array $konteks): bool => isset($konteks['waktu'])))
            ->once();
        $logger->shouldHaveReceived('info')
            ->with('Generate riwayat_pengecekan selesai', Mockery::on(fn (array $konteks): bool => $konteks['jumlah'] === 2))
            ->once();
    }

    public function test_it_is_scheduled_daily_at_midnight_wita_outside_local(): void
    {
        $event = collect($this->app->make(Schedule::class)->events())
            ->first(fn (Event $event): bool => str_contains($event->command, 'riwayat-pengecekan:generate'));

        $this->assertNotNull($event);
        $this->assertSame('0 0 * * *', $event->expression);
        $this->assertSame('Asia/Makassar', $event->timezone);
    }
}
