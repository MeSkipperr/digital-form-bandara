<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\FormAlat;
use App\Models\KategoriAlat;
use App\Models\RiwayatPengecekan;
use App\Models\TemplateForm;
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
            ->assertSeeText(['Inspeksi Alat / Alat', 'XRAY-01', 'Gudang Belakang', 'X-Ray', 'Baik', 'Kelola Kategori', 'Tambah Alat'])
            ->assertSeeText(['Semua kategori', 'Semua status', 'Tambah Kategori', 'Buka Kelola Kategori', 'Menampilkan 1 dari 1 alat'])
            ->assertSee('name="id_kategori_alat" value="'.$kategori->id.'"', false)
            ->assertSee('data-jumlah-alat="1"', false);
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
            ->assertSee('XRAY-02')->assertDontSee('XRAY-01')->assertDontSee('BOMB-01')
            ->assertSeeText('Menampilkan 1 dari 1 alat');
    }

    public function test_index_paginates_25_alat_per_page_and_keeps_the_filters(): void
    {
        $kategori = KategoriAlat::factory()->create();
        Alat::factory()->count(27)->for($kategori, 'kategoriAlat')->bagus()->create();
        Alat::factory()->perluPerbaikan()->create();

        $this->get(route('admin.alat.index'))->assertSeeText('Menampilkan 25 dari 28 alat');

        $this->get(route('admin.alat.index', ['status' => 'bagus', 'halaman' => 2]))
            ->assertSeeText('Menampilkan 2 dari 27 alat')
            ->assertSee(e(route('admin.alat.index', ['status' => 'bagus', 'halaman' => 1])), false);
    }

    public function test_show_renders_detail_and_unknown_alat_is_not_found(): void
    {
        $kategori = KategoriAlat::factory()->create(['nama' => 'X-Ray']);
        $alat = Alat::factory()->for($kategori, 'kategoriAlat')->bagus()->create(['id' => 'XRAY-01', 'model' => 'Xray Pro Max', 'lokasi' => 'Gudang Belakang']);
        $formAlat = FormAlat::factory()->for($alat)->for(TemplateForm::factory()->for($kategori, 'kategoriAlat')->create(['nama' => 'Form Kebersihan Lensa']))->create();

        $this->get(route('admin.alat.show', 'XRAY-01'))
            ->assertOk()
            ->assertSeeText(['Inspeksi Alat / Alat / Detail', 'Detail Alat', 'Xray Pro Max', 'Gudang Belakang', 'Kategori Alat', 'X-Ray', 'Baik'])
            ->assertSeeText(['Ubah Informasi', 'Unduh / Cetak QR', 'Form Pemeriksaan', 'Form untuk kategori X-Ray', 'Lihat Semua Form', 'Form Kebersihan Lensa'])
            ->assertSee(['id="qr-alat"', 'aria-label="Kode QR XRAY-01"', 'data-unduh-qr="qr-alat" data-nama-file="qr-XRAY-01"'], false)
            ->assertSee(route('admin.alat.form.task', ['XRAY-01', $formAlat]), false);

        $this->get('/admin/alat/XRAY-99')->assertNotFound();
    }

    public function test_show_without_assigned_forms_shows_an_empty_state(): void
    {
        Alat::factory()->create(['id' => 'XRAY-01']);

        $this->get(route('admin.alat.show', 'XRAY-01'))
            ->assertOk()
            ->assertSeeText('Belum ada form yang dipasang.');
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
            ->assertSessionHasErrors([
                'id' => 'ID alat ini sudah dipakai.',
                'id_kategori_alat' => 'Kategori tidak ditemukan.',
            ]);
    }

    public function test_store_rejects_an_id_that_cannot_be_used_in_urls(): void
    {
        $kategori = KategoriAlat::factory()->create();

        $this->post(route('admin.alat.store'), ['id' => 'XRAY 01/A', 'id_kategori_alat' => $kategori->id])
            ->assertSessionHasErrors(['id' => 'ID alat hanya boleh berisi huruf, angka, tanda - dan _.']);

        $this->assertDatabaseCount('alat', 0);
    }

    public function test_failed_tambah_reopens_its_modal_with_the_old_input_and_the_error(): void
    {
        KategoriAlat::factory()->create();
        Alat::factory()->create(['id' => 'XRAY-01']);

        $this->from(route('admin.alat.index'))
            ->followingRedirects()
            ->post(route('admin.alat.store'), ['_form' => 'tambah-alat', 'id' => 'XRAY-01', 'model' => 'Model Diketik'])
            ->assertOk()
            ->assertSee('data-buka-ulang', false)
            ->assertSee('value="Model Diketik"', false)
            ->assertSeeText(['ID alat ini sudah dipakai.', 'Kategori wajib dipilih.'])
            ->assertDontSee('role="alert"', false);
    }

    public function test_ubah_modal_carries_the_saved_values_so_simpan_stays_disabled_until_something_changes(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01', 'model' => 'Xray Pro', 'lokasi' => null]);
        $nilaiAsli = json_encode(['id' => 'XRAY-01', 'id_kategori_alat' => $alat->id_kategori_alat, 'model' => 'Xray Pro', 'lokasi' => null]);

        $this->get(route('admin.alat.show', $alat))
            ->assertOk()
            ->assertSee('data-nilai-asli="'.e($nilaiAsli).'"', false);

        $halamanDaftar = $this->get(route('admin.alat.index'))
            ->assertOk()
            ->assertSee('data-nilai-asli="'.e($nilaiAsli).'"', false);

        $this->assertSame(1, substr_count($halamanDaftar->getContent(), 'data-nilai-asli'), 'Only the Ubah form compares against saved values, not Tambah.');
    }

    public function test_update_keeping_the_same_id_changes_the_other_fields(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01']);
        $kategori = KategoriAlat::factory()->create();

        $this->from(route('admin.alat.index'))->put(route('admin.alat.update', $alat), [
            'id' => 'XRAY-01',
            'id_kategori_alat' => $kategori->id,
            'model' => 'Baru',
            'lokasi' => 'Lokasi Baru',
        ])->assertRedirect(route('admin.alat.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('alat', ['id' => 'XRAY-01', 'model' => 'Baru', 'lokasi' => 'Lokasi Baru']);
    }

    public function test_update_changes_the_id_and_moves_the_assigned_forms_to_it(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01']);
        $formAlat = FormAlat::factory()->for($alat)->create();

        $this->from(route('admin.alat.show', $alat))->put(route('admin.alat.update', $alat), [
            'id' => 'XRAY-99',
            'id_kategori_alat' => $alat->id_kategori_alat,
        ])->assertRedirect(route('admin.alat.show', 'XRAY-99'))->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('alat', ['id' => 'XRAY-01']);
        $this->assertDatabaseHas('alat', ['id' => 'XRAY-99']);
        $this->assertDatabaseHas('form_alat', ['id' => $formAlat->id, 'id_alat' => 'XRAY-99']);
    }

    public function test_update_rejects_an_id_used_by_another_alat_and_reopens_its_modal(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01', 'model' => 'Model Lama']);
        Alat::factory()->create(['id' => 'XRAY-02']);

        $this->from(route('admin.alat.show', $alat))
            ->followingRedirects()
            ->put(route('admin.alat.update', $alat), [
                '_form' => 'ubah-alat-XRAY-01',
                'id' => 'XRAY-02',
                'id_kategori_alat' => $alat->id_kategori_alat,
                'model' => 'Model Diketik',
            ])
            ->assertOk()
            ->assertSee('data-buka-ulang', false)
            ->assertSee('value="XRAY-02"', false)
            ->assertSeeText('ID alat ini sudah dipakai.')
            ->assertDontSee('role="alert"', false);

        $this->assertDatabaseHas('alat', ['id' => 'XRAY-01', 'model' => 'Model Lama']);
    }

    public function test_destroy_deletes_alat_with_its_forms_and_pending_riwayat(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01']);
        $formAlat = FormAlat::factory()->for($alat)->create();
        $riwayatTertunda = RiwayatPengecekan::factory()->for($formAlat)->tertunda()->create();

        $this->delete(route('admin.alat.destroy', $alat))
            ->assertRedirect(route('admin.alat.index'))
            ->assertSessionHas('status', 'Alat berhasil dihapus.');

        $this->assertModelMissing($alat);
        $this->assertModelMissing($formAlat);
        $this->assertModelMissing($riwayatTertunda);
    }

    public function test_destroy_keeps_filled_riwayat_with_an_empty_alat_and_the_form_still_there(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01']);
        $template = TemplateForm::factory()->create();
        $formAlat = FormAlat::factory()->for($alat)->for($template, 'templateForm')->create();
        $riwayat = RiwayatPengecekan::factory()->for($formAlat)->bagus()->create();
        $riwayatTertunda = RiwayatPengecekan::factory()->for($formAlat)->tertunda()->create();

        $this->delete(route('admin.alat.destroy', $alat))
            ->assertRedirect(route('admin.alat.index'))
            ->assertSessionHas('status', 'Alat berhasil dihapus.');

        $this->assertModelMissing($alat);
        $this->assertModelMissing($riwayatTertunda);
        $this->assertModelExists($template);
        $this->assertDatabaseHas('form_alat', ['id' => $formAlat->id, 'id_alat' => null, 'id_template_form' => $template->id]);
        $this->assertDatabaseHas('riwayat_pengecekan', ['id' => $riwayat->id, 'id_form_alat' => $formAlat->id]);
    }

    public function test_hapus_modal_tells_that_filled_riwayat_is_kept(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01']);
        RiwayatPengecekan::factory()->for(FormAlat::factory()->for($alat))->kurangBagus()->create();

        $this->get(route('admin.alat.index'))
            ->assertOk()
            ->assertSeeText('Alat XRAY-01 sudah memiliki 1 riwayat pengecekan. Riwayat tetap tersimpan, tetapi data alatnya akan kosong.');
    }

    public function test_tambah_modal_posts_a_new_alat_even_when_the_table_has_rows(): void
    {
        Alat::factory()->create(['id' => 'XRAY-01']);

        $this->get(route('admin.alat.index'))
            ->assertOk()
            ->assertSee('action="'.route('admin.alat.store').'"', false)
            ->assertSee('name="_form" value="tambah-alat"', false);
    }
}
