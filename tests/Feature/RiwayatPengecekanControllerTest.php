<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\FormAlat;
use App\Models\KategoriAlat;
use App\Models\Pengguna;
use App\Models\RiwayatPengecekan;
use App\Models\TemplateForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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
        RiwayatPengecekan::factory()->for($formAlat, 'formAlat')->for(Pengguna::factory()->create(['id' => 'yoga001']), 'pengguna')
            ->bagus()->create(['catatan' => 'Pengecekan rutin selesai.', 'dicek_pada' => '2026-11-14 09:20:00']);

        $this->get(route('admin.riwayat.index'))
            ->assertOk()
            ->assertSeeText([
                'Inspeksi Alat / Riwayat', 'Riwayat Pengecekan', 'Waktu Pengecekan', 'Di Cek Oleh',
                '14/11/2026 09:20', 'Bagus', 'Form Xray Atas', 'XRAY-01', 'Xray Pro Max', 'Pengecekan rutin selesai.', 'yoga001', 'Detail',
                'Detail Riwayat Pengecekan', 'CHK-20261114-', 'Informasi Pengecekan', 'XRAY-01 — Xray Pro Max',
                'Dokumentasi Gambar', 'Belum ada dokumentasi gambar.', 'Tutup', 'Lihat Detail Alat',
                'Menampilkan 1 dari 1 catatan riwayat',
            ]);
    }

    public function test_detail_shows_template_form_image_from_public_storage(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('template-form/ETD.jpeg', 'gambar');
        $template = TemplateForm::factory()->create(['path_gambar' => 'template-form/ETD.jpeg']);
        RiwayatPengecekan::factory()->for(FormAlat::factory()->for($template, 'templateForm'), 'formAlat')->create();

        $this->get(route('admin.riwayat.index'))
            ->assertOk()
            ->assertSee(Storage::disk('public')->url('template-form/ETD.jpeg'))
            ->assertDontSeeText('Belum ada dokumentasi gambar.');
    }

    public function test_pending_riwayat_without_petugas_still_renders(): void
    {
        RiwayatPengecekan::factory()->tertunda()->create();

        $this->get(route('admin.riwayat.index'))->assertOk()->assertSeeText('Tertunda');
    }

    public function test_riwayat_keeps_its_form_when_the_alat_is_deleted(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01', 'model' => 'Xray Pro Max']);
        $template = TemplateForm::factory()->create(['nama' => 'Form Xray Atas']);
        $riwayat = RiwayatPengecekan::factory()->for(FormAlat::factory()->for($alat)->for($template, 'templateForm'), 'formAlat')->bagus()->create();

        $alat->delete();

        $this->assertNull($riwayat->fresh()->formAlat->alat);
        $this->get(route('admin.riwayat.index'))
            ->assertOk()
            ->assertSeeText('Form Xray Atas')
            ->assertDontSeeText(['XRAY-01', 'Xray Pro Max', 'Lihat Detail Alat']);
    }

    public function test_riwayat_keeps_its_alat_when_the_template_form_is_deleted(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01']);
        $template = TemplateForm::factory()->create(['nama' => 'Form Xray Atas']);
        $riwayat = RiwayatPengecekan::factory()->for(FormAlat::factory()->for($alat)->for($template, 'templateForm'), 'formAlat')->bagus()->create();

        $template->delete();

        $this->assertNull($riwayat->fresh()->formAlat->templateForm);
        $this->get(route('admin.riwayat.index'))
            ->assertOk()
            ->assertSeeText(['XRAY-01', 'Lihat Detail Alat'])
            ->assertDontSeeText('Form Xray Atas');
    }

    public function test_riwayat_and_form_alat_stay_when_the_pengguna_is_deleted(): void
    {
        $pengguna = Pengguna::factory()->create(['id' => 'yoga001']);
        $riwayat = RiwayatPengecekan::factory()->for($pengguna, 'pengguna')->bagus()->create();

        $pengguna->delete();

        $this->assertDatabaseHas('riwayat_pengecekan', ['id' => $riwayat->id, 'id_pengguna' => null]);
        $this->get(route('admin.riwayat.index'))->assertOk()->assertDontSeeText('yoga001');
    }

    public function test_riwayat_still_renders_when_its_form_alat_is_deleted(): void
    {
        $riwayat = RiwayatPengecekan::factory()->bagus()->create(['catatan' => 'catatan-yatim']);

        $riwayat->formAlat->delete();

        $this->assertDatabaseHas('riwayat_pengecekan', ['id' => $riwayat->id, 'id_form_alat' => null]);
        $this->get(route('admin.riwayat.index'))->assertOk()->assertSeeText('catatan-yatim');
    }

    public function test_index_shows_empty_state(): void
    {
        $this->get(route('admin.riwayat.index'))
            ->assertOk()
            ->assertSee('Tidak ada riwayat pengecekan yang cocok dengan filter.');
    }

    public function test_filters_by_kategori_status_and_search(): void
    {
        $kategoriXray = KategoriAlat::factory()->create(['nama' => 'X-Ray']);
        $kategoriEtd = KategoriAlat::factory()->create(['nama' => 'ETD']);
        $formA = FormAlat::factory()
            ->for(Alat::factory()->for($kategoriXray, 'kategoriAlat')->create(['id' => 'XRAY-01', 'model' => 'Xray Pro Max']), 'alat')
            ->for(TemplateForm::factory()->create(['nama' => 'Form Xray Atas']), 'templateForm')
            ->create();
        $formB = FormAlat::factory()
            ->for(Alat::factory()->for($kategoriEtd, 'kategoriAlat')->create(['id' => 'ETD-01', 'model' => 'ETD 900']), 'alat')
            ->for(TemplateForm::factory()->create(['nama' => 'Form Uji Fungsi ETD']), 'templateForm')
            ->create();

        RiwayatPengecekan::factory()->for($formA, 'formAlat')->for(Pengguna::factory()->create(['id' => 'yoga001']), 'pengguna')
            ->bagus()->create(['catatan' => 'catatan-alfa']);
        RiwayatPengecekan::factory()->for($formB, 'formAlat')->for(Pengguna::factory()->create(['id' => 'made002']), 'pengguna')
            ->kurangBagus()->create(['catatan' => 'catatan-beta']);

        $this->get(route('admin.riwayat.index', ['status' => 'bagus']))->assertSee('catatan-alfa')->assertDontSee('catatan-beta');
        $this->get(route('admin.riwayat.index', ['kategori' => $kategoriEtd->id]))->assertSee('catatan-beta')->assertDontSee('catatan-alfa');
        $this->get(route('admin.riwayat.index', ['cari' => 'Uji Fungsi']))->assertSee('catatan-beta')->assertDontSee('catatan-alfa');
        $this->get(route('admin.riwayat.index', ['cari' => 'Pro Max']))->assertSee('catatan-alfa')->assertDontSee('catatan-beta');
        $this->get(route('admin.riwayat.index', ['cari' => 'ETD-01']))->assertSee('catatan-beta')->assertDontSee('catatan-alfa');
        $this->get(route('admin.riwayat.index', ['cari' => 'made002']))->assertSee('catatan-beta')->assertDontSee('catatan-alfa');
    }

    public function test_index_paginates(): void
    {
        RiwayatPengecekan::factory()->count(27)->create();

        $this->get(route('admin.riwayat.index'))
            ->assertSeeText('Menampilkan 25 dari 27 catatan riwayat')
            ->assertSee('aria-label="Halaman berikutnya"', false);
        $this->get(route('admin.riwayat.index', ['halaman' => 2]))->assertSeeText('Menampilkan 2 dari 27 catatan riwayat');
    }
}
