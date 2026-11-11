<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_index_lists_alat_with_the_design_content(): void
    {
        $kategori = KategoriAlat::factory()->create(['nama' => 'X-Ray']);
        Alat::factory()->for($kategori, 'kategoriAlat')->create(['id' => 'XRAY-01', 'lokasi' => 'Gudang Belakang']);

        $this->get(route('admin.alat.index'))
            ->assertOk()
            ->assertSeeText(['Kelola Alat', 'XRAY-01', 'Gudang Belakang', 'X-Ray', 'Tambah Alat', 'Semua kategori', 'Semua status', 'Tambah Kategori'])
            ->assertSee('name="id_kategori_alat" value="'.$kategori->id.'"', false);
    }

    public function test_index_shows_empty_state_when_no_alat_matches(): void
    {
        $this->get(route('admin.alat.index', ['cari' => 'tidak-ada']))
            ->assertOk()
            ->assertSee('Tidak ada alat yang cocok dengan filter.');
    }

    public function test_filters_and_search_combine(): void
    {
        $kategori = KategoriAlat::factory()->create();
        Alat::factory()->for($kategori, 'kategoriAlat')->perluPerbaikan()->create(['id' => 'XRAY-02', 'lokasi' => 'Terminal Kargo']);
        Alat::factory()->for($kategori, 'kategoriAlat')->bagus()->create(['id' => 'XRAY-01', 'lokasi' => 'Hanggar Timur']);
        Alat::factory()->perluPerbaikan()->create(['id' => 'BOMB-01', 'lokasi' => 'Lobi Utama']);

        $this->get(route('admin.alat.index', ['status' => 'perlu_perbaikan']))
            ->assertSee('Terminal Kargo')->assertSee('Lobi Utama')->assertDontSee('Hanggar Timur');

        $this->get(route('admin.alat.index', ['kategori' => $kategori->id]))
            ->assertSee('XRAY-01')->assertDontSee('BOMB-01');

        $this->get(route('admin.alat.index', ['cari' => 'xray', 'kategori' => $kategori->id, 'status' => 'perlu_perbaikan']))
            ->assertSee('XRAY-02')->assertDontSee('XRAY-01')->assertDontSee('BOMB-01');
    }

    public function test_show_renders_detail_and_unknown_alat_is_not_found(): void
    {
        $kategori = KategoriAlat::factory()->create(['nama' => 'X-Ray']);
        Alat::factory()->for($kategori, 'kategoriAlat')->bagus()->create(['id' => 'XRAY-01']);

        $this->get(route('admin.alat.show', 'XRAY-01'))
            ->assertOk()
            ->assertSeeText(['Detail Alat', 'Informasi Alat', 'X-Ray', 'Bagus', 'Dibuat pada', 'Unduh / Cetak QR', 'Form Pengecekan']);

        $this->get('/admin/alat/XRAY-99')->assertNotFound();
    }

    public function test_form_and_task_pages_render_for_existing_alat(): void
    {
        Alat::factory()->create(['id' => 'XRAY-01', 'model' => 'Xray Pro Max']);

        $this->get(route('admin.alat.form.index', 'XRAY-01'))
            ->assertOk()->assertSeeText(['Kelola Form', 'XRAY-01, Xray Pro Max']);
        $this->get(route('admin.alat.form.task', ['XRAY-01', 1]))
            ->assertOk()->assertSeeText(['Kelola Task', 'Form Xray Atas']);
        $this->get(route('admin.alat.form.task', ['XRAY-01', 99]))->assertNotFound();
        $this->get(route('admin.alat.form.index', 'XRAY-99'))->assertNotFound();
    }

    public function test_store_creates_alat_with_default_status(): void
    {
        $kategori = KategoriAlat::factory()->create();

        $this->post(route('admin.alat.store'), [
            'id' => 'METL-09',
            'id_kategori_alat' => $kategori->id,
            'model' => 'Garrett',
            'lokasi' => 'Gerbang 1',
        ])->assertRedirect(route('admin.alat.show', 'METL-09'));

        $this->assertDatabaseHas('alat', ['id' => 'METL-09', 'status' => 'bagus', 'id_kategori_alat' => $kategori->id]);
    }

    public function test_store_validates_input(): void
    {
        Alat::factory()->create(['id' => 'XRAY-01']);

        $this->post(route('admin.alat.store'), ['id' => 'XRAY-01', 'id_kategori_alat' => 999])
            ->assertSessionHasErrors(['id', 'id_kategori_alat']);
    }

    public function test_update_changes_fields_but_never_the_id(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01']);
        $kategori = KategoriAlat::factory()->create();

        $this->put(route('admin.alat.update', $alat), [
            'id' => 'HACK-01',
            'id_kategori_alat' => $kategori->id,
            'model' => 'Baru',
            'lokasi' => 'Lokasi Baru',
        ])->assertRedirect();

        $this->assertDatabaseHas('alat', ['id' => 'XRAY-01', 'model' => 'Baru', 'lokasi' => 'Lokasi Baru']);
        $this->assertDatabaseMissing('alat', ['id' => 'HACK-01']);
    }

    public function test_destroy_deletes_alat(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01']);

        $this->delete(route('admin.alat.destroy', $alat))->assertRedirect(route('admin.alat.index'));

        $this->assertDatabaseMissing('alat', ['id' => 'XRAY-01']);
    }
}
