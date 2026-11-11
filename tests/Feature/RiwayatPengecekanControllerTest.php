<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\FormAlat;
use App\Models\Pengguna;
use App\Models\RiwayatPengecekan;
use App\Models\TemplateForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatPengecekanControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_index_lists_riwayat_with_the_design_content(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01', 'model' => 'Xray Pro Max']);
        $template = TemplateForm::factory()->create(['nama' => 'Form Xray Atas', 'path_gambar' => null]);
        $formAlat = FormAlat::factory()->for($alat, 'alat')->for($template, 'templateForm')->create();
        RiwayatPengecekan::factory()->for($formAlat, 'formAlat')->for(Pengguna::factory()->create(['nama' => 'Yoga Pratama']), 'pengguna')
            ->kurangBagus()->create(['catatan' => 'Sensor samping tidak merespons']);

        $this->get(route('admin.riwayat.index'))
            ->assertOk()
            ->assertSeeText(['Riwayat Pengecekan', 'Form Xray Atas', 'XRAY-01', 'Sensor samping tidak merespons', 'Gagal', 'Detail Pengecekan', 'XRAY-01, Xray Pro Max', 'Yoga Pratama', 'Buka Detail Alat']);
    }

    public function test_pending_riwayat_without_petugas_still_renders(): void
    {
        RiwayatPengecekan::factory()->tertunda()->create();

        $this->get(route('admin.riwayat.index'))->assertOk()->assertSeeText('Menunggu');
    }

    public function test_index_shows_empty_state(): void
    {
        $this->get(route('admin.riwayat.index'))
            ->assertOk()
            ->assertSee('Tidak ada riwayat pengecekan yang cocok dengan filter.');
    }

    public function test_filters_by_status_alat_period_and_search(): void
    {
        $alatA = Alat::factory()->create(['id' => 'XRAY-01']);
        $alatB = Alat::factory()->create(['id' => 'BOMB-01']);
        $formA = FormAlat::factory()->for($alatA, 'alat')->create();
        $formB = FormAlat::factory()->for($alatB, 'alat')->create();

        RiwayatPengecekan::factory()->for($formA, 'formAlat')->bagus()->create(['catatan' => 'catatan-alfa', 'dicek_pada' => now()->subDay()]);
        RiwayatPengecekan::factory()->for($formB, 'formAlat')->kurangBagus()->create(['catatan' => 'catatan-beta', 'dicek_pada' => now()->subDays(20)]);

        $this->get(route('admin.riwayat.index', ['status' => 'bagus']))->assertSee('catatan-alfa')->assertDontSee('catatan-beta');
        $this->get(route('admin.riwayat.index', ['alat' => 'BOMB-01']))->assertSee('catatan-beta')->assertDontSee('catatan-alfa');
        $this->get(route('admin.riwayat.index', ['periode' => '7']))->assertSee('catatan-alfa')->assertDontSee('catatan-beta');
        $this->get(route('admin.riwayat.index', ['periode' => '30']))->assertSee('catatan-alfa')->assertSee('catatan-beta');
        $this->get(route('admin.riwayat.index', ['cari' => 'beta']))->assertSee('catatan-beta')->assertDontSee('catatan-alfa');
    }

    public function test_index_paginates(): void
    {
        RiwayatPengecekan::factory()->count(12)->create();

        $this->get(route('admin.riwayat.index'))->assertSeeText('Menampilkan 10 dari 12 riwayat');
        $this->get(route('admin.riwayat.index', ['halaman' => 2]))->assertSeeText('Menampilkan 2 dari 12 riwayat');
    }
}
