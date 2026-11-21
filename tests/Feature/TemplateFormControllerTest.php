<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\FormAlat;
use App\Models\KategoriAlat;
use App\Models\RiwayatPengecekan;
use App\Models\TemplateForm;
use App\Models\Tugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TemplateFormControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');
    }

    public function test_index_lists_form_cards_with_the_design_content(): void
    {
        $kategori = KategoriAlat::factory()->create(['nama' => 'X-Ray']);
        $form = TemplateForm::factory()->for($kategori, 'kategoriAlat')->create(['nama' => 'Form Xray Atas', 'path_gambar' => null]);
        Tugas::factory()->count(5)->for($form, 'templateForm')->create();

        $response = $this->get(route('admin.form.index'))
            ->assertOk()
            ->assertSeeText(['Inspeksi Alat / Form', 'Kelola Form', 'Pilih Form', '1 form tersedia', 'Semua kategori', 'Kelola Kategori'])
            ->assertSeeText(['Form Xray Atas', 'X-Ray', 'Detail', 'Tambah Form', 'Ubah Form', 'Hapus Form', 'Tindakan ini tidak dapat dibatalkan.'])
            ->assertSeeText('Form Xray Atas akan dihapus beserta 5 task di dalamnya.')
            ->assertSee(route('admin.form.tugas.index', $form), false)
            ->assertSee('enctype="multipart/form-data"', false);

        $this->assertSame(5, $response->viewData('daftarForm')->first()->tugas_count);
    }

    public function test_index_filters_by_kategori(): void
    {
        $xray = KategoriAlat::factory()->create(['nama' => 'X-Ray']);
        $etd = KategoriAlat::factory()->create(['nama' => 'ETD']);
        TemplateForm::factory()->for($xray, 'kategoriAlat')->create(['nama' => 'Form Xray Atas']);
        TemplateForm::factory()->for($etd, 'kategoriAlat')->create(['nama' => 'Form Uji Fungsi ETD']);

        $this->get(route('admin.form.index', ['kategori' => $xray->id]))
            ->assertSeeText(['Form Xray Atas', '1 form tersedia'])
            ->assertDontSeeText('Form Uji Fungsi ETD');
    }

    public function test_index_paginates_25_forms_per_page(): void
    {
        TemplateForm::factory()->count(27)->create();

        $this->get(route('admin.form.index'))->assertSeeText(['27 form tersedia', 'Menampilkan 25 dari 27 form']);
        $this->assertCount(2, $this->get(route('admin.form.index', ['halaman' => 2]))->viewData('daftarForm'));
    }

    public function test_index_shows_an_empty_state(): void
    {
        $this->get(route('admin.form.index'))
            ->assertOk()
            ->assertSeeText(['0 form tersedia', 'Tidak ada form untuk kategori ini.', 'Tambah Form']);
    }

    public function test_store_creates_a_form_with_an_uploaded_image(): void
    {
        $kategori = KategoriAlat::factory()->create();

        $response = $this->post(route('admin.form.store'), [
            'nama' => 'Form Xray Atas',
            'id_kategori_alat' => $kategori->id,
            'gambar' => UploadedFile::fake()->create('xray.png', 120, 'image/png'),
        ]);

        $form = TemplateForm::query()->sole();
        $response->assertRedirect(route('admin.form.tugas.index', $form))->assertSessionHas('status', 'Form berhasil ditambahkan.');
        $this->assertSame('Form Xray Atas', $form->nama);
        $this->assertStringStartsWith('template-form/', $form->path_gambar);
        Storage::disk('public')->assertExists($form->path_gambar);
    }

    public function test_store_validates_the_input(): void
    {
        $this->post(route('admin.form.store'), [
            'nama' => '',
            'id_kategori_alat' => 999,
            'gambar' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors(['nama', 'id_kategori_alat', 'gambar']);

        $this->post(route('admin.form.store'), [
            'nama' => 'Form Besar',
            'id_kategori_alat' => KategoriAlat::factory()->create()->id,
            'gambar' => UploadedFile::fake()->create('besar.jpg', 6000, 'image/jpeg'),
        ])->assertSessionHasErrors(['gambar' => 'Ukuran gambar maksimal 5 MB.']);

        $this->assertDatabaseCount('template_form', 0);
    }

    public function test_update_replaces_the_image_and_deletes_the_old_file(): void
    {
        Storage::disk('public')->put('template-form/lama.png', 'gambar');
        $form = TemplateForm::factory()->create(['path_gambar' => 'template-form/lama.png']);
        $kategoriBaru = KategoriAlat::factory()->create();

        $this->put(route('admin.form.update', $form), [
            'nama' => 'Form Xray Samping',
            'id_kategori_alat' => $kategoriBaru->id,
            'gambar' => UploadedFile::fake()->create('baru.jpg', 120, 'image/jpeg'),
        ])->assertRedirect(route('admin.form.index'))->assertSessionHas('status', 'Form berhasil diperbarui.');

        $form->refresh();
        $this->assertSame('Form Xray Samping', $form->nama);
        $this->assertSame($kategoriBaru->id, $form->id_kategori_alat);
        Storage::disk('public')->assertExists($form->path_gambar);
        Storage::disk('public')->assertMissing('template-form/lama.png');
    }

    public function test_update_can_remove_the_image_but_keeps_files_shared_with_other_forms(): void
    {
        Storage::disk('public')->put('template-form/bersama.png', 'gambar');
        $form = TemplateForm::factory()->create(['path_gambar' => 'template-form/bersama.png']);
        TemplateForm::factory()->create(['path_gambar' => 'template-form/bersama.png']);

        $this->put(route('admin.form.update', $form), [
            'nama' => $form->nama,
            'id_kategori_alat' => $form->id_kategori_alat,
            'hapus_gambar' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertNull($form->refresh()->path_gambar);
        Storage::disk('public')->assertExists('template-form/bersama.png');
    }

    public function test_update_without_a_new_image_keeps_the_current_one(): void
    {
        $form = TemplateForm::factory()->create(['path_gambar' => 'template-form/ETD.jpeg']);

        $this->put(route('admin.form.update', $form), [
            'nama' => 'Form Uji Fungsi ETD',
            'id_kategori_alat' => $form->id_kategori_alat,
            'hapus_gambar' => '0',
        ])->assertSessionHasNoErrors();

        $this->assertSame('template-form/ETD.jpeg', $form->refresh()->path_gambar);
    }

    public function test_destroy_deletes_the_form_with_its_tasks(): void
    {
        Storage::disk('public')->put('template-form/hapus.png', 'gambar');
        $form = TemplateForm::factory()->create(['path_gambar' => 'template-form/hapus.png']);
        Tugas::factory()->count(3)->for($form, 'templateForm')->create();

        $this->delete(route('admin.form.destroy', $form))
            ->assertRedirect(route('admin.form.index'))
            ->assertSessionHas('status', 'Form berhasil dihapus.');

        $this->assertModelMissing($form);
        $this->assertDatabaseCount('tugas', 0);
        Storage::disk('public')->assertMissing('template-form/hapus.png');
    }

    public function test_destroy_unassigns_a_form_used_by_alat_and_keeps_its_filled_riwayat(): void
    {
        $form = TemplateForm::factory()->create(['nama' => 'Form Xray Atas']);
        $alat = Alat::factory()->create();
        $formAlatTercatat = FormAlat::factory()->for($alat)->for($form, 'templateForm')->create();
        $riwayat = RiwayatPengecekan::factory()->for($formAlatTercatat)->bagus()->create();
        $riwayatTertunda = RiwayatPengecekan::factory()->for($formAlatTercatat)->tertunda()->create();
        $formAlatKosong = FormAlat::factory()->for($form, 'templateForm')->create();

        $this->get(route('admin.form.index'))
            ->assertSeeText('Form Xray Atas masih dipakai 2 alat dan akan dilepas dari alat tersebut.');

        $this->delete(route('admin.form.destroy', $form))
            ->assertRedirect(route('admin.form.index'))
            ->assertSessionHas('status', 'Form berhasil dihapus.');

        $this->assertModelMissing($form);
        $this->assertModelMissing($formAlatKosong);
        $this->assertModelMissing($riwayatTertunda);
        $this->assertModelExists($alat);
        $this->assertDatabaseHas('form_alat', ['id' => $formAlatTercatat->id, 'id_alat' => $alat->id, 'id_template_form' => null]);
        $this->assertDatabaseHas('riwayat_pengecekan', ['id' => $riwayat->id, 'id_form_alat' => $formAlatTercatat->id]);
        $this->assertCount(0, $alat->formAlat);
    }

    public function test_sidebar_form_menu_links_to_the_form_page(): void
    {
        $this->get(route('admin.dasbor'))
            ->assertSee('href="'.route('admin.form.index').'"', false);
    }
}
