<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Pengguna;
use App\Models\RiwayatPengecekan;
use App\Models\SesiPengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DasborControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_dasbor_renders_the_design_content_when_empty(): void
    {
        $this->get(route('admin.dasbor'))
            ->assertOk()
            ->assertSeeText(['Dasbor', 'Perbandingan Status Alat', 'Jumlah Form yang Diisi Setiap Hari', 'Form Hari Ini', 'Pengguna Aktif', 'Tidak ada pengguna yang sedang daring.']);
    }

    public function test_status_alat_counts_come_from_the_database(): void
    {
        Alat::factory()->count(3)->bagus()->create();
        Alat::factory()->count(2)->perluPerbaikan()->create();

        $this->get(route('admin.dasbor'))
            ->assertViewHas('statusAlat', [
                ['label' => 'Perlu Perbaikan', 'value' => 2, 'color' => '#D97706'],
                ['label' => 'Bagus', 'value' => 3, 'color' => '#16A34A'],
            ]);
    }

    public function test_form_hari_ini_counts_todays_checks_and_all_pending(): void
    {
        RiwayatPengecekan::factory()->count(2)->bagus()->create(['dicek_pada' => now()]);
        RiwayatPengecekan::factory()->kurangBagus()->create(['dicek_pada' => now()]);
        RiwayatPengecekan::factory()->bagus()->create(['dicek_pada' => now()->subDays(2)]);
        RiwayatPengecekan::factory()->count(4)->tertunda()->create();

        $nilai = collect($this->get(route('admin.dasbor'))->viewData('formHariIni'))->pluck('value', 'label');

        $this->assertSame(4, $nilai['Menunggu']);
        $this->assertSame(2, $nilai['Baik']);
        $this->assertSame(1, $nilai['Buruk']);
        $this->assertSame(0, $nilai['Dilewati']);
    }

    public function test_form_per_hari_covers_the_selected_period(): void
    {
        RiwayatPengecekan::factory()->count(3)->bagus()->create(['dicek_pada' => now()]);
        RiwayatPengecekan::factory()->kurangBagus()->create(['dicek_pada' => now()->subDays(10)]);
        RiwayatPengecekan::factory()->tertunda()->create();

        $tujuhHari = $this->get(route('admin.dasbor'))->viewData('formPerHari');
        $this->assertCount(7, $tujuhHari);
        $this->assertSame(3, $tujuhHari[6]['value']);
        $this->assertSame(3, array_sum(array_column($tujuhHari, 'value')));

        $tigaPuluhHari = $this->get(route('admin.dasbor', ['periode' => '30']))->viewData('formPerHari');
        $this->assertCount(30, $tigaPuluhHari);
        $this->assertSame(4, array_sum(array_column($tigaPuluhHari, 'value')));
    }

    public function test_pengguna_aktif_lists_only_active_users_with_a_live_session(): void
    {
        $daring = Pengguna::factory()->create(['id' => 'yoga001', 'nama' => 'Yoga Pratama']);
        $kedaluwarsa = Pengguna::factory()->create(['nama' => 'Sesi Kedaluwarsa']);
        $nonaktif = Pengguna::factory()->nonaktif()->create(['nama' => 'Pengguna Nonaktif']);
        Pengguna::factory()->create(['nama' => 'Tanpa Sesi']);

        SesiPengguna::factory()->for($daring, 'pengguna')->create(['aktif_sampai' => now()->addHour()]);
        SesiPengguna::factory()->for($kedaluwarsa, 'pengguna')->create(['aktif_sampai' => now()->subHour()]);
        SesiPengguna::factory()->for($nonaktif, 'pengguna')->create(['aktif_sampai' => now()->addHour()]);

        $this->get(route('admin.dasbor'))
            ->assertSeeText(['Yoga Pratama', '@yoga001', 'Daring'])
            ->assertDontSeeText(['Sesi Kedaluwarsa', 'Pengguna Nonaktif', 'Tanpa Sesi']);
    }
}
