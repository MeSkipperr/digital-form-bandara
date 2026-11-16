<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\FormAlat;
use App\Models\KategoriAlat;
use App\Models\RiwayatPengecekan;
use App\Models\TemplateForm;
use App\Models\Tugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormAlatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_index_lists_assigned_forms_and_offers_the_unassigned_forms_of_the_kategori(): void
    {
        $kategori = KategoriAlat::factory()->create(['nama' => 'X-Ray']);
        $alat = Alat::factory()->for($kategori, 'kategoriAlat')->create(['id' => 'XRAY-01', 'model' => 'Xray Pro Max']);
        $terpasang = TemplateForm::factory()->for($kategori, 'kategoriAlat')->has(Tugas::factory()->count(2))->create(['nama' => 'Form Xray Atas']);
        FormAlat::factory()->for($alat)->for($terpasang)->create();
        TemplateForm::factory()->for($kategori, 'kategoriAlat')->create(['nama' => 'Form Kelistrikan']);
        TemplateForm::factory()->create(['nama' => 'Form Kategori Lain']);

        $response = $this->get(route('admin.alat.form.index', $alat));

        $response->assertOk()
            ->assertSeeText(['Kelola Form', 'XRAY-01, Xray Pro Max', 'Form Xray Atas', '2 task', 'Tambah Form', 'Lepas Form'])
            ->assertSee('name="id_template_form[]"', false)
            ->assertSeeText('Form Kelistrikan')
            ->assertDontSeeText('Form Kategori Lain');
        $this->assertSame(1, substr_count($response->getContent(), 'name="id_template_form[]"'));
    }

    public function test_index_of_an_unknown_alat_is_not_found(): void
    {
        $this->get(route('admin.alat.form.index', 'XRAY-99'))->assertNotFound();
    }

    public function test_tambah_modal_explains_when_no_form_is_left_to_assign(): void
    {
        $alat = Alat::factory()->for(KategoriAlat::factory()->create(['nama' => 'X-Ray']), 'kategoriAlat')->create();

        $this->get(route('admin.alat.form.index', $alat))
            ->assertOk()
            ->assertSeeText(['Belum ada form untuk kategori X-Ray.', 'Buka Halaman Form'])
            ->assertDontSee('name="id_template_form[]"', false);
    }

    public function test_store_assigns_the_checked_forms_to_the_alat(): void
    {
        $alat = Alat::factory()->create(['id' => 'XRAY-01']);
        $formAtas = TemplateForm::factory()->for($alat->kategoriAlat, 'kategoriAlat')->create();
        $formSamping = TemplateForm::factory()->for($alat->kategoriAlat, 'kategoriAlat')->create();

        $this->post(route('admin.alat.form.store', $alat), ['id_template_form' => [$formAtas->id, $formSamping->id]])
            ->assertRedirect(route('admin.alat.form.index', $alat))
            ->assertSessionHas('status', 'Form berhasil dipasang ke alat.');

        $this->assertDatabaseHas('form_alat', ['id_alat' => 'XRAY-01', 'id_template_form' => $formAtas->id]);
        $this->assertDatabaseHas('form_alat', ['id_alat' => 'XRAY-01', 'id_template_form' => $formSamping->id]);
    }

    public function test_store_requires_at_least_one_form(): void
    {
        $alat = Alat::factory()->create();

        $this->post(route('admin.alat.form.store', $alat), [])
            ->assertSessionHasErrors(['id_template_form' => 'Pilih minimal satu form.']);

        $this->assertDatabaseCount('form_alat', 0);
    }

    public function test_store_rejects_a_form_of_another_kategori(): void
    {
        $alat = Alat::factory()->create();
        $formLain = TemplateForm::factory()->create();

        $this->post(route('admin.alat.form.store', $alat), ['id_template_form' => [$formLain->id]])
            ->assertSessionHasErrors(['id_template_form.0' => 'Form tidak ditemukan untuk kategori alat ini.']);

        $this->assertDatabaseCount('form_alat', 0);
    }

    public function test_store_rejects_a_form_already_assigned_to_the_alat(): void
    {
        $formAlat = FormAlat::factory()->create();
        $alat = $formAlat->alat;
        $formAlat->templateForm->update(['id_kategori_alat' => $alat->id_kategori_alat]);

        $this->post(route('admin.alat.form.store', $alat), ['id_template_form' => [$formAlat->id_template_form]])
            ->assertSessionHasErrors(['id_template_form.0' => 'Form ini sudah dipasang di alat.']);

        $this->assertDatabaseCount('form_alat', 1);
    }

    public function test_show_lists_the_tasks_of_the_assigned_form(): void
    {
        $formAlat = FormAlat::factory()->for(Alat::factory()->state(['id' => 'XRAY-01']))->for(TemplateForm::factory()->state(['nama' => 'Form Xray Atas']))->create();
        Tugas::factory()->for($formAlat->templateForm, 'templateForm')->create(['nama' => 'Periksa kabel daya']);
        Tugas::factory()->create(['nama' => 'Task form lain']);

        $this->get(route('admin.alat.form.task', [$formAlat->alat, $formAlat]))
            ->assertOk()
            ->assertSeeText(['Kelola Task', 'Form Xray Atas', 'XRAY-01, 1 task', 'Periksa kabel daya'])
            ->assertDontSeeText('Task form lain')
            ->assertSee('href="'.route('admin.form.tugas.index', $formAlat->templateForm).'"', false);
    }

    public function test_show_of_a_form_assigned_to_another_alat_is_not_found(): void
    {
        $alat = Alat::factory()->create();
        $formAlatLain = FormAlat::factory()->create();

        $this->get(route('admin.alat.form.task', [$alat, $formAlatLain]))->assertNotFound();
        $this->get(route('admin.alat.form.task', [$alat, 999]))->assertNotFound();
    }

    public function test_destroy_unassigns_the_form_with_its_pending_riwayat(): void
    {
        $formAlat = FormAlat::factory()->for(TemplateForm::factory()->state(['nama' => 'Form Xray Atas']))->create();
        $riwayatTertunda = RiwayatPengecekan::factory()->for($formAlat)->tertunda()->create();

        $this->delete(route('admin.alat.form.destroy', [$formAlat->alat, $formAlat]))
            ->assertRedirect(route('admin.alat.form.index', $formAlat->alat))
            ->assertSessionHas('status', 'Form Xray Atas berhasil dilepas dari alat.');

        $this->assertModelMissing($formAlat);
        $this->assertModelMissing($riwayatTertunda);
        $this->assertModelExists($formAlat->templateForm);
    }

    public function test_destroy_refuses_a_form_with_filled_riwayat(): void
    {
        $formAlat = FormAlat::factory()->for(TemplateForm::factory()->state(['nama' => 'Form Xray Atas']))->create();
        $riwayat = RiwayatPengecekan::factory()->for($formAlat)->terlewati()->create();

        $this->delete(route('admin.alat.form.destroy', [$formAlat->alat, $formAlat]))
            ->assertRedirect(route('admin.alat.form.index', $formAlat->alat))
            ->assertSessionHasErrors(['form' => 'Form Xray Atas sudah memiliki riwayat pengecekan sehingga tidak dapat dilepas dari alat.']);

        $this->assertModelExists($formAlat);
        $this->assertModelExists($riwayat);
    }

    public function test_destroy_of_a_form_assigned_to_another_alat_is_not_found(): void
    {
        $alat = Alat::factory()->create();
        $formAlatLain = FormAlat::factory()->create();

        $this->delete(route('admin.alat.form.destroy', [$alat, $formAlatLain]))->assertNotFound();

        $this->assertModelExists($formAlatLain);
    }
}
